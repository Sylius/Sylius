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

namespace Tests\Sylius\Bundle\ApiBundle\Doctrine\ORM\Filter;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Exception\InvalidArgumentException;
use ApiPlatform\Metadata\Exception\ItemNotFoundException;
use ApiPlatform\Metadata\IriConverterInterface;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\Filter\TaxonFilter;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;

final class TaxonFilterTest extends TestCase
{
    /** @var ManagerRegistry|MockObject */
    private MockObject $managerRegistryMock;

    /** @var IriConverterInterface|MockObject */
    private MockObject $iriConverterMock;

    private TaxonFilter $taxonFilter;

    protected function setUp(): void
    {
        $this->managerRegistryMock = $this->createMock(ManagerRegistry::class);
        $this->iriConverterMock = $this->createMock(IriConverterInterface::class);
        $this->taxonFilter = new TaxonFilter($this->managerRegistryMock, $this->iriConverterMock);
    }

    public function testAddsTaxonFilterIfPropertyIsTaxon(): void
    {
        /** @var TaxonInterface|MockObject $taxonMock */
        $taxonMock = $this->createMock(TaxonInterface::class);
        /** @var TaxonInterface|MockObject $taxonRootMock */
        $taxonRootMock = $this->createMock(TaxonInterface::class);
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->iriConverterMock->expects(self::once())->method('getResourceFromIri')->with('api/taxon')->willReturn($taxonMock);
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->willReturn(['o']);
        $queryBuilderMock->expects(self::once())->method('distinct')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects(self::once())->method('addSelect')->with('productTaxon')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->exactly(2))->method('innerJoin')->willReturnMap([['o.productTaxons', 'productTaxon', $queryBuilderMock], ['productTaxon.taxon', 'taxon', $queryBuilderMock]]);
        $queryBuilderMock->expects(self::once())->method('andWhere')->with('taxon.left >= :taxonLeft')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->exactly(3))->method('andWhere')->willReturnMap([['taxon.left >= :taxonLeft', $queryBuilderMock], ['taxon.right <= :taxonRight', $queryBuilderMock], ['taxon.root = :taxonRoot', $queryBuilderMock]]);
        $taxonMock->expects(self::once())->method('getRoot')->willReturn($taxonRootMock);
        $taxonMock->expects(self::once())->method('getLeft')->willReturn(3);
        $taxonMock->expects(self::once())->method('getRight')->willReturn(5);
        $queryBuilderMock->expects(self::once())->method('setParameter')->with('taxonLeft', 3)->willReturn($queryBuilderMock);
        $queryBuilderMock->expects(self::once())->method('setParameter')->with('taxonRight', 5)->willReturn($queryBuilderMock);
        $queryBuilderMock->expects(self::once())->method('setParameter')->with('taxonRoot', $taxonRootMock)->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->exactly(3))->method('setParameter')->willReturnMap([['taxonLeft', 3, $queryBuilderMock], ['taxonRight', 5, $queryBuilderMock], ['taxonRoot', $taxonRootMock, $queryBuilderMock]]);
    }

    public function testDoesNotAddTheDefaultOrderByTaxonPositionIfADifferentOrderParameterIsSpecified(): void
    {
        /** @var TaxonInterface|MockObject $taxonMock */
        $taxonMock = $this->createMock(TaxonInterface::class);
        /** @var TaxonInterface|MockObject $taxonRootMock */
        $taxonRootMock = $this->createMock(TaxonInterface::class);
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $context['filters']['order'] = ['differentOrderParameter' => 'asc'];
        $this->iriConverterMock->expects(self::once())->method('getResourceFromIri')->with('api/taxon')->willReturn($taxonMock);
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->willReturn(['o']);
        $queryBuilderMock->expects(self::once())->method('distinct')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects(self::once())->method('addSelect')->with('productTaxon')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->exactly(2))->method('innerJoin')->willReturnMap([['o.productTaxons', 'productTaxon', $queryBuilderMock], ['productTaxon.taxon', 'taxon', $queryBuilderMock]]);
        $queryBuilderMock->expects(self::once())->method('andWhere')->with('taxon.root = :taxonRoot')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects(self::never())->method('addOrderBy')->with('productTaxon.position');
        $taxonMock->expects(self::once())->method('getRoot')->willReturn($taxonRootMock);
        $taxonMock->expects(self::once())->method('getLeft')->willReturn(null);
        $taxonMock->expects(self::once())->method('getRight')->willReturn(null);
        $queryBuilderMock->expects(self::once())->method('setParameter')->with('taxonRoot', $taxonRootMock)->willReturn($queryBuilderMock);
        $this->taxonFilter->filterProperty(
            'taxon',
            'api/taxon',
            $queryBuilderMock,
            $queryNameGeneratorMock,
            'resourceClass',
            context: $context,
        );
    }

    public function testDoesNotAddTheDefaultOrderByTaxonPositionIfTaxonDoesNotExist(): void
    {
        /** @var TaxonInterface|MockObject $taxonMock */
        $taxonMock = $this->createMock(TaxonInterface::class);
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->iriConverterMock->expects(self::once())->method('getResourceFromIri')->with('api/taxon')->willThrowException(ItemNotFoundException::class);
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->willReturn(['o']);
        $queryBuilderMock->expects(self::once())->method('distinct')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects(self::once())->method('addSelect')->with('productTaxon')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->exactly(2))->method('innerJoin')->willReturnMap([['o.productTaxons', 'productTaxon', $queryBuilderMock], ['productTaxon.taxon', 'taxon', $queryBuilderMock]]);
        $queryBuilderMock->expects(self::once())->method('andWhere')->with('taxon.root = :taxonRoot')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects(self::never())->method('addOrderBy')->with('productTaxon.position');
        $taxonMock->expects(self::never())->method('getRoot');
        $queryBuilderMock->expects(self::once())->method('setParameter')->with('taxonRoot', null)->willReturn($queryBuilderMock);
        $this->taxonFilter->filterProperty(
            'taxon',
            'api/taxon',
            $queryBuilderMock,
            $queryNameGeneratorMock,
            'resourceClass',
        );
    }

    public function testDoesNotAddTheDefaultOrderByTaxonPositionIfTaxonIsGivenWithWrongFormat(): void
    {
        /** @var TaxonInterface|MockObject $taxonMock */
        $taxonMock = $this->createMock(TaxonInterface::class);
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->iriConverterMock->expects(self::once())->method('getResourceFromIri')->with('non-existing-taxon')->willThrowException(InvalidArgumentException::class);
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->willReturn(['o']);
        $queryBuilderMock->expects(self::once())->method('distinct')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects(self::once())->method('addSelect')->with('productTaxon')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->exactly(2))->method('innerJoin')->willReturnMap([['o.productTaxons', 'productTaxon', $queryBuilderMock], ['productTaxon.taxon', 'taxon', $queryBuilderMock]]);
        $queryBuilderMock->expects(self::once())->method('andWhere')->with('taxon.root = :taxonRoot')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects(self::never())->method('addOrderBy')->with('productTaxon.position');
        $taxonMock->expects(self::never())->method('getRoot');
        $queryBuilderMock->expects(self::once())->method('setParameter')->with('taxonRoot', null)->willReturn($queryBuilderMock);
        $this->taxonFilter->filterProperty(
            'taxon',
            'non-existing-taxon',
            $queryBuilderMock,
            $queryNameGeneratorMock,
            'resourceClass',
        );
    }
}
