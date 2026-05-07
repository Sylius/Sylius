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

namespace Tests\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\ProductAssociation;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Get;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\ProductAssociation\EnabledProductsExtension;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Symfony\Component\HttpFoundation\Request;

final class EnabledProductsExtensionTest extends TestCase
{
    private EnabledProductsExtension $extension;

    private MockObject&SectionProviderInterface $sectionProvider;

    private MockObject&QueryBuilder $queryBuilder;

    private MockObject&QueryNameGeneratorInterface $queryNameGenerator;

    protected function setUp(): void
    {
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->queryBuilder = $this->createMock(QueryBuilder::class);
        $this->queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $this->extension = new EnabledProductsExtension($this->sectionProvider);
    }

    public function test_it_does_nothing_if_current_resource_is_not_a_product_association(): void
    {
        $this->sectionProvider->expects($this->never())->method('getSection');
        $this->queryBuilder->expects($this->never())->method('getRootAliases');

        $this->extension->applyToItem(
            $this->queryBuilder,
            $this->queryNameGenerator,
            ProductVariantInterface::class,
            [],
            new Get(),
        );
    }

    public function test_it_does_nothing_if_section_is_not_shop_api(): void
    {
        $section = $this->createMock(AdminApiSection::class);
        $this->sectionProvider->expects($this->once())->method('getSection')->willReturn($section);

        $this->queryBuilder->expects($this->never())->method('innerJoin');
        $this->queryBuilder->expects($this->never())->method('andWhere');
        $this->queryBuilder->expects($this->never())->method('setParameter');

        $this->extension->applyToItem(
            $this->queryBuilder,
            $this->queryNameGenerator,
            ProductAssociationInterface::class,
            [],
            new Get(),
        );
    }

    public function test_it_applies_conditions_for_customer(): void
    {
        $section = $this->createMock(ShopApiSection::class);
        $channel = $this->createMock(ChannelInterface::class);

        $this->sectionProvider->method('getSection')->willReturn($section);

        $this->queryNameGenerator->expects($this->exactly(2))
            ->method('generateParameterName')
            ->willReturnMap([
                ['enabled', 'enabled'],
                ['channel', 'channel'],
            ]);

        $this->queryBuilder->expects($this->once())
            ->method('getRootAliases')
            ->willReturn(['o']);

        $this->queryBuilder->expects($this->once())
            ->method('addSelect')
            ->with('associatedProduct')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder->expects($this->once())
            ->method('leftJoin')
            ->with('o.associatedProducts', 'associatedProduct', 'WITH', 'associatedProduct.enabled = :enabled')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder->expects($this->once())
            ->method('innerJoin')
            ->with('associatedProduct.channels', 'channel', 'WITH', 'channel = :channel')
            ->willReturn($this->queryBuilder);

        $this->extension->applyToItem(
            $this->queryBuilder,
            $this->queryNameGenerator,
            ProductAssociationInterface::class,
            [],
            new Get(),
            [
                ContextKeys::CHANNEL => $channel,
                ContextKeys::HTTP_REQUEST_METHOD_TYPE => Request::METHOD_GET,
            ],
        );
    }
}
