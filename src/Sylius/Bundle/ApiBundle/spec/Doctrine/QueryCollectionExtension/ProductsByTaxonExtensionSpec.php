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

namespace spec\Sylius\Bundle\ApiBundle\Doctrine\QueryCollectionExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\ContextAwareQueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Andx;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class ProductsByTaxonExtensionSpec extends ObjectBehavior
{
    function let(UserContextInterface $userContext): void
    {
        $this->beConstructedWith($userContext);
    }

    public function it_is_a_constraint_validator()
    {
        $this->shouldHaveType(ContextAwareQueryCollectionExtensionInterface::class);
    }

    function it_does_nothing_if_current_resource_is_not_a_product(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $userContext->getUser()->shouldNotBeCalled();
        $queryBuilder->getRootAliases()->shouldNotBeCalled();

        $this->applyToCollection($queryBuilder, $queryNameGenerator, TaxonInterface::class, 'get', ['filters' => ['productTaxons.taxon.code' => 't_shirts']]);
    }

    function it_does_nothing_if_current_user_is_an_admin_user(
        UserContextInterface $userContext,
        UserInterface $user,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $userContext->getUser()->willReturn($user);
        $user->getRoles()->willReturn(['ROLE_API_ACCESS']);

        $queryBuilder->getRootAliases()->shouldNotBeCalled();

        $this->applyToCollection($queryBuilder, $queryNameGenerator, ProductInterface::class, 'get', ['filters' => ['productTaxons.taxon.code' => 't_shirts']]);
    }

    function it_does_nothing_if_filter_is_not_set(
        UserContextInterface $userContext,
        UserInterface $user,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $userContext->getUser()->willReturn($user);
        $user->getRoles()->willReturn([]);

        $queryBuilder->getRootAliases()->shouldNotBeCalled();

        $this->applyToCollection($queryBuilder, $queryNameGenerator, ProductInterface::class, 'get', []);
    }

    function it_filters_products_by_taxon(
        UserContextInterface $userContext,
        UserInterface $user,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        Expr $expr,
        Expr\Comparison $comparison,
        Andx $andx,
    ): void {
        $userContext->getUser()->willReturn($user);
        $user->getRoles()->willReturn([]);

        $queryNameGenerator->generateParameterName('taxonCode')->shouldBeCalled()->willReturn('taxonCode');
        $queryNameGenerator->generateJoinAlias('productTaxons')->shouldBeCalled()->willReturn('productTaxons');
        $queryNameGenerator->generateJoinAlias('taxon')->shouldBeCalled()->willReturn('taxon');

        $queryBuilder->getRootAliases()->willReturn(['o']);
        $queryBuilder->addSelect('productTaxons')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->leftJoin('o.productTaxons', 'productTaxons', 'WITH', 'productTaxons.product = o.id')->shouldBeCalled()->willReturn($queryBuilder);
        $expr->andX(Argument::type(Expr\Comparison::class), Argument::type(Expr\Comparison::class))->willReturn($andx);
        $expr->in('taxon.code', ':taxonCode')->shouldBeCalled()->willReturn($comparison);
        $expr->eq('taxon.enabled', 'true')->shouldBeCalled()->willReturn($comparison);
        $queryBuilder->expr()->willReturn($expr->getWrappedObject());
        $queryBuilder->leftJoin('productTaxons.taxon', 'taxon', 'WITH', Argument::type(Andx::class))->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->orderBy('productTaxons.position', 'ASC')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setParameter('taxonCode', ['t_shirts'])->shouldBeCalled()->willReturn($queryBuilder);

        $this->applyToCollection($queryBuilder, $queryNameGenerator, ProductInterface::class, 'get', ['filters' => ['productTaxons.taxon.code' => 't_shirts']]);
    }
}
