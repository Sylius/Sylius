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

namespace Tests\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\PaymentMethod;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\PaymentMethod\ChannelBasedExtension;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;

final class ChannelBasedExtensionTest extends TestCase
{
    private ChannelBasedExtension $extension;

    private MockObject&SectionProviderInterface $sectionProvider;

    private MockObject&QueryBuilder $queryBuilder;

    private MockObject&QueryNameGeneratorInterface $queryNameGenerator;

    protected function setUp(): void
    {
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->queryBuilder = $this->createMock(QueryBuilder::class);
        $this->queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $this->extension = new ChannelBasedExtension($this->sectionProvider);
    }

    public function test_it_filters_payment_method_by_current_channel(): void
    {
        $channel = $this->createMock(ChannelInterface::class);

        $this->sectionProvider->method('getSection')->willReturn(new ShopApiSection());

        $this->queryNameGenerator->expects(self::once())
            ->method('generateParameterName')
            ->with('channel')
            ->willReturn('channel');

        $this->queryBuilder->method('getRootAliases')->willReturn(['o']);
        $this->queryBuilder->expects(self::once())
            ->method('andWhere')
            ->with(':channel MEMBER OF o.channels')
            ->willReturnSelf();

        $this->queryBuilder->expects(self::once())
            ->method('setParameter')
            ->with('channel', $channel)
            ->willReturnSelf();

        $this->extension->applyToItem(
            $this->queryBuilder,
            $this->queryNameGenerator,
            PaymentMethodInterface::class,
            [],
            new Get(),
            [ContextKeys::CHANNEL => $channel],
        );
    }

    public function test_it_filters_payment_methods_by_current_channel(): void
    {
        $channel = $this->createMock(ChannelInterface::class);

        $this->sectionProvider->method('getSection')->willReturn(new ShopApiSection());

        $this->queryNameGenerator->expects(self::once())
            ->method('generateParameterName')
            ->with('channel')
            ->willReturn('channel');

        $this->queryBuilder->method('getRootAliases')->willReturn(['o']);
        $this->queryBuilder->expects(self::once())
            ->method('andWhere')
            ->with(':channel MEMBER OF o.channels')
            ->willReturnSelf();

        $this->queryBuilder->expects(self::once())
            ->method('setParameter')
            ->with('channel', $channel)
            ->willReturnSelf();

        $this->extension->applyToCollection(
            $this->queryBuilder,
            $this->queryNameGenerator,
            PaymentMethodInterface::class,
            new GetCollection(),
            [ContextKeys::CHANNEL => $channel],
        );
    }

    public function test_it_does_nothing_if_resource_is_not_payment_method_for_item(): void
    {
        $this->sectionProvider->method('getSection')->willReturn(new ShopApiSection());

        $this->queryBuilder->expects(self::never())->method('getRootAliases');
        $this->queryBuilder->expects(self::never())->method('andWhere');

        $this->extension->applyToItem(
            $this->queryBuilder,
            $this->queryNameGenerator,
            \stdClass::class,
            [],
            new Get(),
        );
    }

    public function test_it_does_nothing_if_resource_is_not_payment_method_for_collection(): void
    {
        $this->sectionProvider->method('getSection')->willReturn(new ShopApiSection());

        $this->queryBuilder->expects(self::never())->method('getRootAliases');
        $this->queryBuilder->expects(self::never())->method('andWhere');

        $this->extension->applyToCollection(
            $this->queryBuilder,
            $this->queryNameGenerator,
            \stdClass::class,
            new GetCollection(),
        );
    }

    public function test_it_does_nothing_if_section_is_not_shop_for_item(): void
    {
        $this->sectionProvider->method('getSection')->willReturn(new AdminApiSection());

        $this->queryBuilder->expects(self::never())->method('getRootAliases');
        $this->queryBuilder->expects(self::never())->method('andWhere');

        $this->extension->applyToItem(
            $this->queryBuilder,
            $this->queryNameGenerator,
            ShippingMethodInterface::class,
            [],
            new Get(),
        );
    }

    public function test_it_does_nothing_if_section_is_not_shop_for_collection(): void
    {
        $this->sectionProvider->method('getSection')->willReturn(new AdminApiSection());

        $this->queryBuilder->expects(self::never())->method('getRootAliases');
        $this->queryBuilder->expects(self::never())->method('andWhere');

        $this->extension->applyToCollection(
            $this->queryBuilder,
            $this->queryNameGenerator,
            ShippingMethodInterface::class,
            new GetCollection(),
        );
    }
}
