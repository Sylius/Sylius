<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Product;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Get;
use Doctrine\ORM\QueryBuilder;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Product\ChannelAndLocaleBasedExtension;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;

final class ChannelAndLocaleBasedExtensionTest extends TestCase
{
    private ChannelAndLocaleBasedExtension $extension;

    private MockObject&SectionProviderInterface $sectionProvider;

    private MockObject&QueryBuilder $queryBuilder;

    private MockObject&QueryNameGeneratorInterface $queryNameGenerator;

    protected function setUp(): void
    {
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->queryBuilder = $this->createMock(QueryBuilder::class);
        $this->queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $this->extension = new ChannelAndLocaleBasedExtension($this->sectionProvider);
    }

    public function test_it_does_not_apply_conditions_to_collection_for_unsupported_resource(): void
    {
        $this->queryBuilder->expects(self::never())->method('getRootAliases');
        $this->queryBuilder->expects(self::never())->method('andWhere');

        $this->extension->applyToCollection(
            $this->queryBuilder,
            $this->queryNameGenerator,
            \stdClass::class,
        );
    }

    public function test_it_does_not_apply_conditions_to_collection_for_admin_api_section(): void
    {
        $this->sectionProvider->method('getSection')->willReturn(new AdminApiSection());

        $this->queryBuilder->expects(self::never())->method('getRootAliases');
        $this->queryBuilder->expects(self::never())->method('andWhere');

        $this->extension->applyToCollection(
            $this->queryBuilder,
            $this->queryNameGenerator,
            AddressInterface::class,
        );
    }

    public function test_it_throws_exception_if_context_has_no_channel(): void
    {
        $this->sectionProvider->method('getSection')->willReturn(new ShopApiSection());

        $this->expectException(InvalidArgumentException::class);

        $this->extension->applyToCollection(
            $this->queryBuilder,
            $this->queryNameGenerator,
            ProductInterface::class,
            new Get(),
        );
    }

    public function test_it_throws_exception_if_context_has_no_locale(): void
    {
        $this->sectionProvider->method('getSection')->willReturn(new ShopApiSection());

        $channel = $this->createMock(ChannelInterface::class);

        $this->expectException(InvalidArgumentException::class);

        $this->extension->applyToCollection(
            $this->queryBuilder,
            $this->queryNameGenerator,
            ProductInterface::class,
            new Get(),
            [ContextKeys::CHANNEL => $channel],
        );
    }

    public function test_it_filters_products_by_channel_and_locale(): void
    {
        $this->sectionProvider->method('getSection')->willReturn(new ShopApiSection());

        $channel = $this->createMock(ChannelInterface::class);

        $this->queryNameGenerator->expects(self::exactly(2))
            ->method('generateParameterName')
            ->with($this->callback(function ($param) {
                return $param === 'channel' || $param === 'localeCode';
            }))
            ->willReturnCallback(function ($param) {
                return $param;
            });

        $this->queryBuilder->method('getRootAliases')->willReturn(['o']);

        $this->queryBuilder->expects(self::once())
            ->method('addSelect')
            ->with('translation')
            ->willReturnSelf();

        $this->queryBuilder->expects(self::once())
            ->method('innerJoin')
            ->with('o.translations', 'translation', 'WITH', 'translation.locale = :localeCode')
            ->willReturnSelf();

        $this->queryBuilder->expects(self::once())
            ->method('andWhere')
            ->with(':channel MEMBER OF o.channels')
            ->willReturnSelf();

        $expectedParams = [
            ['channel', $channel],
            ['localeCode', 'en_US'],
        ];
        $callIndex = 0;
        $this->queryBuilder->expects(self::exactly(2))
            ->method('setParameter')
            ->with(
                $this->callback(function ($name) use (&$expectedParams, &$callIndex) {
                    return $name === $expectedParams[$callIndex][0];
                }),
                $this->callback(function ($value) use (&$expectedParams, &$callIndex) {
                    $result = $value === $expectedParams[$callIndex][1];
                    ++$callIndex;

                    return $result;
                }),
            )
            ->willReturnSelf();

        $this->extension->applyToCollection(
            $this->queryBuilder,
            $this->queryNameGenerator,
            ProductInterface::class,
            new Get(),
            [
                ContextKeys::CHANNEL => $channel,
                ContextKeys::LOCALE_CODE => 'en_US',
            ],
        );
    }
}
