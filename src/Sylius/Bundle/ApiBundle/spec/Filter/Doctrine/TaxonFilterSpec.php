<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ApiBundle\Filter\Doctrine;

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;

final class TaxonFilterSpec extends ObjectBehavior
{
    function let(ManagerRegistry $managerRegistry, IriConverterInterface $iriConverter): void
    {
        $this->beConstructedWith($managerRegistry, $iriConverter);
    }

    function it_adds_taxon_filter_if_property_is_taxon(
        IriConverterInterface $iriConverter,
        TaxonInterface $taxon,
        TaxonInterface $taxonRoot,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $iriConverter->getItemFromIri('api/taxon')->willReturn($taxon);
        $queryBuilder->getRootAliases()->willReturn(['o']);

        $queryBuilder->distinct()->willReturn($queryBuilder);
        $queryBuilder->addSelect('productTaxon')->willReturn($queryBuilder);
        $queryBuilder->innerJoin('o.productTaxons', 'productTaxon')->willReturn($queryBuilder);
        $queryBuilder->innerJoin('productTaxon.taxon', 'taxon')->willReturn($queryBuilder);
        $queryBuilder->andWhere('taxon.left >= :taxonLeft')->willReturn($queryBuilder);
        $queryBuilder->andWhere('taxon.right <= :taxonRight')->willReturn($queryBuilder);
        $queryBuilder->andWhere('taxon.root = :taxonRoot')->willReturn($queryBuilder);
        $queryBuilder->addOrderBy('productTaxon.position')->willReturn($queryBuilder);

        $taxon->getRoot()->willReturn($taxonRoot);
        $taxon->getLeft()->willReturn(3);
        $taxon->getRight()->willReturn(5);
        $queryBuilder->setParameter('taxonLeft', 3)->willReturn($queryBuilder);
        $queryBuilder->setParameter('taxonRight', 5)->willReturn($queryBuilder);
        $queryBuilder->setParameter('taxonRoot', $taxonRoot)->willReturn($queryBuilder);

        $this->filterProperty(
            'taxon',
            'api/taxon',
            $queryBuilder,
            $queryNameGenerator,
            'resourceClass',
        );
    }
}
