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
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Model\ProductAssociationInterface;

final class EnabledProductsExtensionTest extends TestCase
{
    /** @var SectionProviderInterface|MockObject */
    private MockObject $sectionProviderMock;

    private EnabledProductsExtension $enabledProductsExtension;

    protected function setUp(): void
    {
        $this->sectionProviderMock = $this->createMock(SectionProviderInterface::class);
        $this->enabledProductsExtension = new EnabledProductsExtension($this->sectionProviderMock);
    }

    public function testDoesNothingIfCurrentResourceIsNotAProductAssociation(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->sectionProviderMock->expects(self::never())->method('getSection');
        $queryBuilderMock->expects(self::never())->method('getRootAliases');
        $this->enabledProductsExtension->applyToItem(
            $queryBuilderMock,
            $queryNameGeneratorMock,
            ProductVariantInterface::class,
            [],
            new Get(),
        );
    }

    public function testDoesNothingIfSectionIsNotShopApi(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        /** @var AdminApiSection|MockObject $sectionMock */
        $sectionMock = $this->createMock(AdminApiSection::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($sectionMock);
        $this->enabledProductsExtension->applyToItem(
            $queryBuilderMock,
            $queryNameGeneratorMock,
            ProductAssociationInterface::class,
            [],
            new Get(),
        );
    }

    public function testAppliesConditionsForCustomer(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        /** @var ShopApiSection|MockObject $sectionMock */
        $sectionMock = $this->createMock(ShopApiSection::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($sectionMock);
        $queryNameGeneratorMock->expects($this->exactly(2))->method('generateParameterName')->willReturnMap([['enabled', 'enabled'], ['channel', 'channel']]);
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->willReturn(['o']);
        $queryBuilderMock->expects(self::once())->method('addSelect')->with('associatedProduct')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects(self::once())->method('leftJoin')->with('o.associatedProducts', 'associatedProduct', 'WITH', 'associatedProduct.enabled = :enabled')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects(self::once())->method('innerJoin')->with('associatedProduct.channels', 'channel', 'WITH', 'channel = :channel')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects(self::once())->method('setParameter')->with('enabled', true)->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->exactly(2))->method('setParameter')->willReturnMap([['enabled', true, $queryBuilderMock], ['channel', $channelMock, $queryBuilderMock]]);
    }
}
