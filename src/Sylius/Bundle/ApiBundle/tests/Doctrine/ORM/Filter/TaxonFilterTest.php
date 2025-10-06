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
    private TaxonFilter $taxonFilter;

    /** @var MockObject&IriConverterInterface */
    private $iriConverter;

    /** @var MockObject&ManagerRegistry */
    private $managerRegistry;

    protected function setUp(): void
    {
        $this->managerRegistry = $this->createMock(ManagerRegistry::class);
        $this->iriConverter = $this->createMock(IriConverterInterface::class);

        $this->taxonFilter = new TaxonFilter($this->managerRegistry, $this->iriConverter);
    }

    public function test_it_adds_taxon_filter_if_property_is_taxon(): void
    {
        $taxon = $this->createMock(TaxonInterface::class);
        $taxonRoot = $this->createMock(TaxonInterface::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $this->iriConverter
            ->method('getResourceFromIri')
            ->with('api/taxon')
            ->willReturn($taxon);

        $queryBuilder->method('getRootAliases')->willReturn(['o']);

        $queryBuilder->method('distinct')->willReturnSelf();
        $queryBuilder->method('addSelect')->willReturnSelf();
        $queryBuilder->method('innerJoin')->willReturnSelf();
        $queryBuilder->method('andWhere')->willReturnSelf();
        $queryBuilder->method('setParameter')->willReturnSelf();

        $queryBuilder
            ->expects($this->once())
            ->method('addOrderBy')
            ->with(
                $this->equalTo('productTaxon.position'),
                $this->isNull(),
            )
            ->willReturnSelf();

        $taxon->method('getRoot')->willReturn($taxonRoot);
        $taxon->method('getLeft')->willReturn(3);
        $taxon->method('getRight')->willReturn(5);

        $this->taxonFilter->filterProperty('taxon', 'api/taxon', $queryBuilder, $queryNameGenerator, 'resourceClass');
    }

    public function test_it_does_not_add_order_by_if_different_order_parameter_specified(): void
    {
        $taxon = $this->createMock(TaxonInterface::class);
        $taxonRoot = $this->createMock(TaxonInterface::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);
        $context = ['filters' => ['order' => ['differentOrderParameter' => 'asc']]];

        $this->iriConverter
            ->method('getResourceFromIri')
            ->with('api/taxon')
            ->willReturn($taxon);

        $queryBuilder->method('getRootAliases')->willReturn(['o']);
        $queryBuilder->method('distinct')->willReturnSelf();
        $queryBuilder->method('addSelect')->willReturnSelf();
        $queryBuilder->method('innerJoin')->willReturnSelf();
        $queryBuilder->method('andWhere')->willReturnSelf();
        $queryBuilder->method('setParameter')->willReturnSelf();

        $taxon->method('getRoot')->willReturn($taxonRoot);
        $taxon->method('getLeft')->willReturn(null);
        $taxon->method('getRight')->willReturn(null);

        $queryBuilder->expects($this->never())->method('addOrderBy');

        $this->taxonFilter->filterProperty(
            'taxon',
            'api/taxon',
            $queryBuilder,
            $queryNameGenerator,
            'resourceClass',
            context: $context,
        );
    }

    public function test_it_does_not_add_order_by_if_taxon_does_not_exist(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $this->iriConverter
            ->method('getResourceFromIri')
            ->with('api/taxon')
            ->willThrowException(new ItemNotFoundException());

        $queryBuilder->method('getRootAliases')->willReturn(['o']);
        $queryBuilder->method('distinct')->willReturnSelf();
        $queryBuilder->method('addSelect')->willReturnSelf();
        $queryBuilder->method('innerJoin')->willReturnSelf();
        $queryBuilder->method('andWhere')->willReturnSelf();
        $queryBuilder->method('setParameter')->willReturnSelf();

        $queryBuilder->expects($this->never())->method('addOrderBy');

        $this->taxonFilter->filterProperty(
            'taxon',
            'api/taxon',
            $queryBuilder,
            $queryNameGenerator,
            'resourceClass',
        );
    }

    public function test_it_does_not_add_order_by_if_taxon_is_in_wrong_format(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $this->iriConverter
            ->method('getResourceFromIri')
            ->with('non-existing-taxon')
            ->willThrowException(new InvalidArgumentException());

        $queryBuilder->method('getRootAliases')->willReturn(['o']);
        $queryBuilder->method('distinct')->willReturnSelf();
        $queryBuilder->method('addSelect')->willReturnSelf();
        $queryBuilder->method('innerJoin')->willReturnSelf();
        $queryBuilder->method('andWhere')->willReturnSelf();
        $queryBuilder->method('setParameter')->willReturnSelf();

        $queryBuilder->expects($this->never())->method('addOrderBy');

        $this->taxonFilter->filterProperty(
            'taxon',
            'non-existing-taxon',
            $queryBuilder,
            $queryNameGenerator,
            'resourceClass',
        );
    }
}
