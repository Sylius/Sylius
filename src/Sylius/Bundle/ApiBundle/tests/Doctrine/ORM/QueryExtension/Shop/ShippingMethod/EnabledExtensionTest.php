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

namespace Tests\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\ShippingMethod;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\ShippingMethod\EnabledExtension;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;

final class EnabledExtensionTest extends TestCase
{
    private EnabledExtension $extension;

    private MockObject&SectionProviderInterface $sectionProvider;

    private MockObject&QueryBuilder $queryBuilder;

    private MockObject&QueryNameGeneratorInterface $queryNameGenerator;

    protected function setUp(): void
    {
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->queryBuilder = $this->createMock(QueryBuilder::class);
        $this->queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $this->extension = new EnabledExtension($this->sectionProvider);
    }

    public function test_it_filters_enabled_shipping_method(): void
    {
        $this->sectionProvider->method('getSection')->willReturn(new ShopApiSection());

        $this->queryNameGenerator->expects($this->once())
            ->method('generateParameterName')
            ->with('enabled')
            ->willReturn('enabled');

        $this->queryBuilder->expects($this->once())
            ->method('getRootAliases')
            ->willReturn(['o']);

        $this->queryBuilder->expects($this->once())
            ->method('andWhere')
            ->with('o.enabled = :enabled')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder->expects($this->once())
            ->method('setParameter')
            ->with('enabled', true)
            ->willReturn($this->queryBuilder);

        $this->extension->applyToItem(
            $this->queryBuilder,
            $this->queryNameGenerator,
            ShippingMethodInterface::class,
            [],
            new Get(),
        );
    }

    public function test_it_filters_enabled_shipping_methods(): void
    {
        $this->sectionProvider->method('getSection')->willReturn(new ShopApiSection());

        $this->queryNameGenerator->expects($this->once())
            ->method('generateParameterName')
            ->with('enabled')
            ->willReturn('enabled');

        $this->queryBuilder->expects($this->once())
            ->method('getRootAliases')
            ->willReturn(['o']);

        $this->queryBuilder->expects($this->once())
            ->method('andWhere')
            ->with('o.enabled = :enabled')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder->expects($this->once())
            ->method('setParameter')
            ->with('enabled', true)
            ->willReturn($this->queryBuilder);

        $this->extension->applyToCollection(
            $this->queryBuilder,
            $this->queryNameGenerator,
            ShippingMethodInterface::class,
            new GetCollection(),
        );
    }

    public function test_it_does_nothing_if_the_resource_is_not_shipping_method_for_item(): void
    {
        $this->sectionProvider->method('getSection')->willReturn(new ShopApiSection());

        $this->queryBuilder->expects($this->never())->method('getRootAliases');
        $this->queryBuilder->expects($this->never())->method('andWhere');

        $this->extension->applyToItem(
            $this->queryBuilder,
            $this->queryNameGenerator,
            \stdClass::class,
            [],
            new Get(),
        );
    }

    public function test_it_does_nothing_if_the_resource_is_not_shipping_method_for_collection(): void
    {
        $this->sectionProvider->method('getSection')->willReturn(new ShopApiSection());

        $this->queryBuilder->expects($this->never())->method('getRootAliases');
        $this->queryBuilder->expects($this->never())->method('andWhere');

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

        $this->queryBuilder->expects($this->never())->method('getRootAliases');
        $this->queryBuilder->expects($this->never())->method('andWhere');

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

        $this->queryBuilder->expects($this->never())->method('getRootAliases');
        $this->queryBuilder->expects($this->never())->method('andWhere');

        $this->extension->applyToCollection(
            $this->queryBuilder,
            $this->queryNameGenerator,
            ShippingMethodInterface::class,
            new GetCollection(),
        );
    }
}
