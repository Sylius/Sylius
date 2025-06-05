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
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Andx;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\Query\Expr\Func;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use stdClass;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Product\TaxonBasedExtension;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ProductInterface;

final class TaxonBasedExtensionTest extends TestCase
{
    /** @var SectionProviderInterface|MockObject */
    private MockObject $sectionProviderMock;

    private TaxonBasedExtension $taxonBasedExtension;

    protected function setUp(): void
    {
        $this->sectionProviderMock = $this->createMock(SectionProviderInterface::class);
        $this->taxonBasedExtension = new TaxonBasedExtension($this->sectionProviderMock);
    }

    public function testDoesNotApplyConditionsToCollectionForUnsupportedResource(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->taxonBasedExtension->applyToCollection($queryBuilderMock, $queryNameGeneratorMock, stdClass::class);
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
        $this->taxonBasedExtension->applyToCollection($queryBuilderMock, $queryNameGeneratorMock, AddressInterface::class);
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->shouldNotHaveBeenCalled();
        $queryBuilderMock->expects(self::once())->method('andWhere')->shouldNotHaveBeenCalled();
    }

    public function testDoesNothingIfFilterIsNotSet(): void
    {
        /** @var ShopApiSection|MockObject $shopApiSectionMock */
        $shopApiSectionMock = $this->createMock(ShopApiSection::class);
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($shopApiSectionMock);
        $queryBuilderMock->expects(self::never())->method('getRootAliases');
        $this->taxonBasedExtension->applyToCollection($queryBuilderMock, $queryNameGeneratorMock, ProductInterface::class, new Get());
    }

    public function testFiltersProductsByTaxon(): void
    {
        /** @var ShopApiSection|MockObject $shopApiSectionMock */
        $shopApiSectionMock = $this->createMock(ShopApiSection::class);
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        /** @var Expr|MockObject $exprMock */
        $exprMock = $this->createMock(Expr::class);
        /** @var Func|MockObject $exprInMock */
        $exprInMock = $this->createMock(Func::class);
        /** @var Comparison|MockObject $exprEqMock */
        $exprEqMock = $this->createMock(Comparison::class);
        /** @var Andx|MockObject $exprAndxMock */
        $exprAndxMock = $this->createMock(Andx::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($shopApiSectionMock);
        $queryNameGeneratorMock->expects(self::once())->method('generateParameterName')->with('taxonCode')->willReturn('taxonCode');
        $queryNameGeneratorMock->expects($this->exactly(2))->method('generateJoinAlias')->willReturnMap([['productTaxons', 'productTaxons'], ['taxon', 'taxon']]);
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->willReturn(['o']);
        $queryBuilderMock->expects(self::once())->method('addSelect')->with('productTaxons')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects(self::once())->method('leftJoin')->with('o.productTaxons', 'productTaxons', 'WITH', 'productTaxons.product = o.id')
            ->willReturn($queryBuilderMock)
        ;
        $queryBuilderMock->expects($this->exactly(2))->method('leftJoin')->willReturnMap([['o.productTaxons', 'productTaxons', 'WITH', 'productTaxons.product = o.id', $queryBuilderMock], ['productTaxons.taxon', 'taxon', 'WITH', Argument::type(Andx::class), $queryBuilderMock]]);
        $exprMock->expects(self::once())->method('eq')->with('taxon.enabled', 'true')->willReturn($exprEqMock);
        $queryBuilderMock->expects(self::once())->method('expr')->willReturn($exprMock);
        $queryBuilderMock->expects(self::once())->method('leftJoin')->with('productTaxons.taxon', 'taxon', 'WITH', $this->isInstanceOf(Andx::class))
            ->willReturn($queryBuilderMock)
        ;
        $queryBuilderMock->expects(self::once())->method('orderBy')->with('productTaxons.position', 'ASC')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects(self::once())->method('setParameter')->with('taxonCode', ['t_shirts'])->willReturn($queryBuilderMock);
        $this->taxonBasedExtension->applyToCollection(
            $queryBuilderMock,
            $queryNameGeneratorMock,
            ProductInterface::class,
            new Get(),
            ['filters' => ['productTaxons.taxon.code' => 't_shirts']],
        );
    }
}
