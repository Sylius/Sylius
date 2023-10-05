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

namespace spec\Sylius\Bundle\ApiBundle\Filter\Doctrine;

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Exception\ItemNotFoundException;
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

    function it_does_not_add_the_default_order_by_taxon_position_if_a_different_order_parameter_is_specified(
        IriConverterInterface $iriConverter,
        TaxonInterface $taxon,
        TaxonInterface $taxonRoot,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $context['filters']['order'] = ['differentOrderParameter' => 'asc'];
        $iriConverter->getItemFromIri('api/taxon')->willReturn($taxon);
        $queryBuilder->getRootAliases()->willReturn(['o']);

        $queryBuilder->distinct()->willReturn($queryBuilder);
        $queryBuilder->addSelect('productTaxon')->willReturn($queryBuilder);
        $queryBuilder->innerJoin('o.productTaxons', 'productTaxon')->willReturn($queryBuilder);
        $queryBuilder->innerJoin('productTaxon.taxon', 'taxon')->willReturn($queryBuilder);
        $queryBuilder->andWhere('taxon.root = :taxonRoot')->willReturn($queryBuilder);
        $queryBuilder->addOrderBy('productTaxon.position')->shouldNotBeCalled();

        $taxon->getRoot()->willReturn($taxonRoot);
        $taxon->getLeft()->willReturn(null);
        $taxon->getRight()->willReturn(null);
        $queryBuilder->setParameter('taxonRoot', $taxonRoot)->willReturn($queryBuilder);

        $this->filterProperty(
            'taxon',
            'api/taxon',
            $queryBuilder,
            $queryNameGenerator,
            'resourceClass',
            context: $context,
        );
    }

    function it_does_not_add_the_default_order_by_taxon_position_if_taxon_does_not_exist(
        IriConverterInterface $iriConverter,
        TaxonInterface $taxon,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $iriConverter->getItemFromIri('api/taxon')->willThrow(ItemNotFoundException::class);
        $queryBuilder->getRootAliases()->willReturn(['o']);

        $queryBuilder->distinct()->willReturn($queryBuilder);
        $queryBuilder->addSelect('productTaxon')->willReturn($queryBuilder);
        $queryBuilder->innerJoin('o.productTaxons', 'productTaxon')->willReturn($queryBuilder);
        $queryBuilder->innerJoin('productTaxon.taxon', 'taxon')->willReturn($queryBuilder);
        $queryBuilder->andWhere('taxon.root = :taxonRoot')->willReturn($queryBuilder);
        $queryBuilder->addOrderBy('productTaxon.position')->shouldNotBeCalled();

        $taxon->getRoot()->shouldNotBeCalled();
        $queryBuilder->setParameter('taxonRoot', null)->willReturn($queryBuilder);

        $this->filterProperty(
            'taxon',
            'api/taxon',
            $queryBuilder,
            $queryNameGenerator,
            'resourceClass',
        );
    }

    function it_does_not_add_the_default_order_by_taxon_position_if_taxon_is_given_with_wrong_format(
        IriConverterInterface $iriConverter,
        TaxonInterface $taxon,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $iriConverter->getItemFromIri('non-existing-taxon')->willThrow(InvalidArgumentException::class);
        $queryBuilder->getRootAliases()->willReturn(['o']);

        $queryBuilder->distinct()->willReturn($queryBuilder);
        $queryBuilder->addSelect('productTaxon')->willReturn($queryBuilder);
        $queryBuilder->innerJoin('o.productTaxons', 'productTaxon')->willReturn($queryBuilder);
        $queryBuilder->innerJoin('productTaxon.taxon', 'taxon')->willReturn($queryBuilder);
        $queryBuilder->andWhere('taxon.root = :taxonRoot')->willReturn($queryBuilder);
        $queryBuilder->addOrderBy('productTaxon.position')->shouldNotBeCalled();

        $taxon->getRoot()->shouldNotBeCalled();

        $queryBuilder->setParameter('taxonRoot', null)->willReturn($queryBuilder);

        $this->filterProperty(
            'taxon',
            'non-existing-taxon',
            $queryBuilder,
            $queryNameGenerator,
            'resourceClass',
        );
    }
}
