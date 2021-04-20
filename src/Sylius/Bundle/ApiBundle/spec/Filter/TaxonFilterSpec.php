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

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Core\Bridge\Symfony\Routing\IriConverter;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;

final class TaxonFilterSpec extends ObjectBehavior
{
    function let(IriConverter $iriConverter, ManagerRegistry $managerRegistry): void
    {
        $this->beConstructedWith($iriConverter, $managerRegistry);
    }

    function it_adds_taxon_filter_if_property_is_product_taxon(
        IriConverter $iriConverter,
        TaxonInterface $taxon,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator
    ): void {
        $iriConverter->getItemFromIri('api/taxon')->willReturn($taxon);
        $queryBuilder->getRootAliases()->shouldNotBeCalled();
        $queryNameGenerator->generateParameterName('taxon')->willReturn('taxon1');

        $queryBuilder
            ->join('o.productTaxons', 'p')
            ->shouldBeCalled()
            ->willReturn($queryBuilder)
        ;

        $queryBuilder
            ->andWhere('p.taxon = taxon1')
            ->shouldBeCalled()
            ->willReturn($queryBuilder)
        ;

        $queryBuilder
            ->setParameter('taxon1', $taxon)
            ->shouldBeCalled()
            ->willReturn($queryBuilder)
        ;

        $this->filterProperty('productTaxon', 'api/taxon', $queryBuilder, $queryNameGenerator, Argument::type('string'));
    }
}
