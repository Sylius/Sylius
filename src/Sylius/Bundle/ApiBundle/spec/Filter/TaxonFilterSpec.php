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

namespace spec\Sylius\Bundle\ApiBundle\Filter;

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;

final class TaxonFilterSpec extends ObjectBehavior
{
    function let(IriConverterInterface $iriConverter, ManagerRegistry $managerRegistry): void
    {
        $this->beConstructedWith($iriConverter, $managerRegistry);
    }

    function it_adds_taxon_filter_if_property_is_product_taxon(
        IriConverterInterface $iriConverter,
        TaxonInterface $taxon,
        TaxonInterface $taxonRoot,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator
    ): void {
        $iriConverter->getItemFromIri('api/taxon')->willReturn($taxon);
        $queryBuilder->getRootAliases()->willReturn(['o']);

        $queryBuilder
            ->join('o.productTaxons', 'p')
            ->shouldBeCalled()
            ->willReturn($queryBuilder)
        ;

        $queryBuilder
            ->innerJoin('p.taxon', 'taxon')
            ->shouldBeCalled()
            ->willReturn($queryBuilder)
        ;

        $queryBuilder
            ->andWhere('taxon.left >= :taxonLeft')
            ->shouldBeCalled()
            ->willReturn($queryBuilder)
        ;

        $queryBuilder
            ->andWhere('taxon.right <= :taxonRight')
            ->shouldBeCalled()
            ->willReturn($queryBuilder)
        ;

        $queryBuilder
            ->andWhere('taxon.root = :taxonRoot')
            ->shouldBeCalled()
            ->willReturn($queryBuilder)
        ;

        $taxon->getLeft()->willReturn(1);

        $queryBuilder
            ->setParameter('taxonLeft', 1)
            ->shouldBeCalled()
            ->willReturn($queryBuilder)
        ;

        $taxon->getRight()->willReturn(3);

        $queryBuilder
            ->setParameter('taxonRight', 3)
            ->shouldBeCalled()
            ->willReturn($queryBuilder)
        ;

        $taxon->getRoot()->willReturn($taxonRoot);

        $queryBuilder
            ->setParameter('taxonRoot', $taxonRoot)
            ->shouldBeCalled()
            ->willReturn($queryBuilder)
        ;

        $this->filterProperty(
            'productTaxons',
            'api/taxon',
            $queryBuilder,
            $queryNameGenerator,
            'resourceClass'
        );
    }
}
