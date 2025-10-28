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

namespace Tests\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Locale;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Get;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Locale\ChannelBasedExtension;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Symfony\Component\HttpFoundation\Request;

final class ChannelBasedExtensionTest extends TestCase
{
    private ChannelBasedExtension $extension;

    private MockObject&SectionProviderInterface $sectionProvider;

    private MockObject&QueryBuilder $queryBuilder;

    private MockObject&QueryNameGeneratorInterface $nameGenerator;

    protected function setUp(): void
    {
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->queryBuilder = $this->createMock(QueryBuilder::class);
        $this->nameGenerator = $this->createMock(QueryNameGeneratorInterface::class);
        $this->extension = new ChannelBasedExtension($this->sectionProvider);
    }

    public function test_does_not_apply_conditions_to_collection_for_unsupported_resource(): void
    {
        $this->sectionProvider->expects($this->never())->method('getSection');
        $this->queryBuilder->expects($this->never())->method('getRootAliases');
        $this->queryBuilder->expects($this->never())->method('andWhere');

        $this->extension->applyToCollection($this->queryBuilder, $this->nameGenerator, stdClass::class);
    }

    public function test_does_not_apply_conditions_for_non_shop_api_section(): void
    {
        $adminApiSection = $this->createMock(AdminApiSection::class);
        $channel = $this->createMock(ChannelInterface::class);

        $this->sectionProvider->expects($this->once())->method('getSection')->willReturn($adminApiSection);
        $this->queryBuilder->expects($this->never())->method('getRootAliases');
        $this->queryBuilder->expects($this->never())->method('andWhere');

        $this->extension->applyToCollection(
            $this->queryBuilder,
            $this->nameGenerator,
            LocaleInterface::class,
            new Get(),
            [
                ContextKeys::CHANNEL => $channel,
                ContextKeys::HTTP_REQUEST_METHOD_TYPE => Request::METHOD_GET,
            ],
        );
    }

    public function test_applies_conditions_for_shop_api_section(): void
    {
        $shopApiSection = $this->createMock(ShopApiSection::class);
        $channel = $this->createMock(ChannelInterface::class);
        $locale = $this->createMock(LocaleInterface::class);

        $this->sectionProvider->expects($this->once())->method('getSection')->willReturn($shopApiSection);
        $this->nameGenerator->expects($this->once())->method('generateParameterName')->with('locales')->willReturn('locales');

        $locales = new ArrayCollection([$locale]);
        $channel->method('getLocales')->willReturn($locales);

        $this->queryBuilder->expects($this->once())->method('getRootAliases')->willReturn(['o']);
        $this->queryBuilder->expects($this->once())->method('andWhere')->with('o.id in (:locales)')->willReturn($this->queryBuilder);
        $this->queryBuilder->expects($this->once())->method('setParameter')->with('locales', $locales)->willReturn($this->queryBuilder);

        $this->extension->applyToCollection(
            $this->queryBuilder,
            $this->nameGenerator,
            LocaleInterface::class,
            new Get(),
            [
                ContextKeys::CHANNEL => $channel,
                ContextKeys::HTTP_REQUEST_METHOD_TYPE => Request::METHOD_GET,
            ],
        );
    }

    public function test_throws_an_exception_if_context_has_no_channel(): void
    {
        $shopApiSection = $this->createMock(ShopApiSection::class);

        $this->sectionProvider->expects($this->once())->method('getSection')->willReturn($shopApiSection);

        $this->expectException(InvalidArgumentException::class);

        $this->extension->applyToCollection(
            $this->queryBuilder,
            $this->nameGenerator,
            LocaleInterface::class,
            new Get(),
        );
    }
}
