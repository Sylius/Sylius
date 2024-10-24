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

namespace spec\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Product;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Get;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Andx;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ProductInterface;

final class TaxonBasedExtensionSpec extends ObjectBehavior
{
    function let(SectionProviderInterface $sectionProvider): void
    {
        $this->beConstructedWith($sectionProvider);
    }

    public function it_is_a_constraint_validator()
    {
        $this->shouldHaveType(QueryCollectionExtensionInterface::class);
    }

    public function it_does_not_apply_conditions_to_collection_for_unsupported_resource(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $this->applyToCollection($queryBuilder, $queryNameGenerator, \stdClass::class);

        $queryBuilder->getRootAliases()->shouldNotHaveBeenCalled();
        $queryBuilder->andWhere()->shouldNotHaveBeenCalled();
    }

    function it_does_not_apply_conditions_to_collection_for_admin_api_section(
        SectionProviderInterface $sectionProvider,
        AdminApiSection $adminApiSection,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $sectionProvider->getSection()->willReturn($adminApiSection);

        $this->applyToCollection($queryBuilder, $queryNameGenerator, AddressInterface::class);

        $queryBuilder->getRootAliases()->shouldNotHaveBeenCalled();
        $queryBuilder->andWhere()->shouldNotHaveBeenCalled();
    }

    function it_does_nothing_if_filter_is_not_set(
        SectionProviderInterface $sectionProvider,
        ShopApiSection $shopApiSection,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $sectionProvider->getSection()->willReturn($shopApiSection);

        $queryBuilder->getRootAliases()->shouldNotBeCalled();

        $this->applyToCollection($queryBuilder, $queryNameGenerator, ProductInterface::class, new Get());
    }

    function it_filters_products_by_taxon(
        SectionProviderInterface $sectionProvider,
        ShopApiSection $shopApiSection,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        Expr $expr,
        Expr\Comparison $comparison,
        Andx $andx,
    ): void {
        $sectionProvider->getSection()->willReturn($shopApiSection);

        $queryNameGenerator->generateParameterName('taxonCode')->shouldBeCalled()->willReturn('taxonCode');
        $queryNameGenerator->generateJoinAlias('productTaxons')->shouldBeCalled()->willReturn('productTaxons');
        $queryNameGenerator->generateJoinAlias('taxon')->shouldBeCalled()->willReturn('taxon');

        $queryBuilder->getRootAliases()->willReturn(['o']);
        $queryBuilder->addSelect('productTaxons')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder
            ->leftJoin('o.productTaxons', 'productTaxons', 'WITH', 'productTaxons.product = o.id')
            ->shouldBeCalled()
            ->willReturn($queryBuilder)
        ;
        $expr->andX(Argument::type(Expr\Comparison::class), Argument::type(Expr\Comparison::class))->willReturn($andx);
        $expr->in('taxon.code', ':taxonCode')->shouldBeCalled()->willReturn($comparison);
        $expr->eq('taxon.enabled', 'true')->shouldBeCalled()->willReturn($comparison);
        $queryBuilder->expr()->willReturn($expr->getWrappedObject());
        $queryBuilder
            ->leftJoin('productTaxons.taxon', 'taxon', 'WITH', Argument::type(Andx::class))
            ->willReturn($queryBuilder)
        ;
        $queryBuilder->orderBy('productTaxons.position', 'ASC')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setParameter('taxonCode', ['t_shirts'])->shouldBeCalled()->willReturn($queryBuilder);

        $this->applyToCollection(
            $queryBuilder,
            $queryNameGenerator,
            ProductInterface::class,
            new Get(),
            ['filters' => ['productTaxons.taxon.code' => 't_shirts']],
        );
    }
}
