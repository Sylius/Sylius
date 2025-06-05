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

namespace Tests\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Taxon;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Get;
use Doctrine\ORM\QueryBuilder;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Taxon\ChannelBasedExtension;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;

final class ChannelBasedExtensionTest extends TestCase
{
    /** @var SectionProviderInterface|MockObject */
    private MockObject $sectionProviderMock;

    private ChannelBasedExtension $channelBasedExtension;

    protected function setUp(): void
    {
        $this->sectionProviderMock = $this->createMock(SectionProviderInterface::class);
        $this->channelBasedExtension = new ChannelBasedExtension($this->sectionProviderMock);
    }

    public function testDoesNotApplyConditionsToCollectionForUnsupportedResource(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->channelBasedExtension->applyToCollection($queryBuilderMock, $queryNameGeneratorMock, stdClass::class);
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->shouldNotHaveBeenCalled();
        $queryBuilderMock->expects(self::once())->method('andWhere')->shouldNotHaveBeenCalled();
    }

    public function testDoesNotApplyConditionsToCollectionForAdminApiSection(): void
    {
        /** @var AdminApiSection|MockObject $adminApiSectionMock */
        $adminApiSectionMock = $this->createMock(AdminApiSection::class);
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($adminApiSectionMock);
        $this->channelBasedExtension->applyToCollection($queryBuilderMock, $queryNameGeneratorMock, AddressInterface::class);
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->shouldNotHaveBeenCalled();
        $queryBuilderMock->expects(self::once())->method('andWhere')->shouldNotHaveBeenCalled();
    }

    public function testThrowsAnExceptionIfContextHasNotChannel(): void
    {
        /** @var ShopApiSection|MockObject $shopApiSectionMock */
        $shopApiSectionMock = $this->createMock(ShopApiSection::class);
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($shopApiSectionMock);
        $this->expectException(InvalidArgumentException::class);
        $this->channelBasedExtension->applyToCollection($queryBuilderMock, $queryNameGeneratorMock, TaxonInterface::class, new Get());
    }

    public function testAppliesConditionsForShopApiSection(): void
    {
        /** @var TaxonRepositoryInterface|MockObject $taxonRepositoryMock */
        $taxonRepositoryMock = $this->createMock(TaxonRepositoryInterface::class);
        /** @var ShopApiSection|MockObject $shopApiSectionMock */
        $shopApiSectionMock = $this->createMock(ShopApiSection::class);
        /** @var TaxonInterface|MockObject $menuTaxonMock */
        $menuTaxonMock = $this->createMock(TaxonInterface::class);
        /** @var TaxonInterface|MockObject $firstTaxonMock */
        $firstTaxonMock = $this->createMock(TaxonInterface::class);
        /** @var TaxonInterface|MockObject $secondTaxonMock */
        $secondTaxonMock = $this->createMock(TaxonInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($shopApiSectionMock);
        $channelMock->expects(self::once())->method('getMenuTaxon')->willReturn($menuTaxonMock);
        $menuTaxonMock->expects(self::once())->method('getCode')->willReturn('code');
        $queryNameGeneratorMock->expects($this->exactly(2))->method('generateParameterName')->willReturnMap([['parentCode', 'parentCode'], ['enabled', 'enabled']]);
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->willReturn(['o']);
        $queryBuilderMock->expects(self::once())->method('addSelect')->with('child')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects(self::once())->method('innerJoin')->with('o.parent', 'parent')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects(self::once())->method('leftJoin')->with('o.children', 'child', 'WITH', 'child.enabled = true')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects(self::once())->method('andWhere')->with('o.enabled = :enabled')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->exactly(2))->method('andWhere')->willReturnMap([['o.enabled = :enabled', $queryBuilderMock], ['parent.code = :parentCode', $queryBuilderMock]]);
        $queryBuilderMock->expects(self::once())->method('setParameter')->with('parentCode', 'code')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects(self::once())->method('setParameter')->with('enabled', true)->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->exactly(2))->method('setParameter')->willReturnMap([['parentCode', 'code', $queryBuilderMock], ['enabled', true, $queryBuilderMock]]);
    }
}
