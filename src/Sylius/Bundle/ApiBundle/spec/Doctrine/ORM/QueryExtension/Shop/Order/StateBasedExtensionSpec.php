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

namespace spec\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Order;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

final class StateBasedExtensionSpec extends ObjectBehavior
{
    function let(SectionProviderInterface $sectionProvider): void
    {
        $this->beConstructedWith($sectionProvider, ['_api_/shop/orders/{tokenValue}_get', '_api_/shop/orders/{tokenValue}/payments/{paymentId}/configuration_get']);
    }

    function it_does_not_apply_conditions_to_collection_for_unsupported_resource(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $queryBuilder->getRootAliases()->shouldNotBeCalled();

        $this->applyToCollection($queryBuilder, $queryNameGenerator, ResourceInterface::class, new Get());
    }

    function it_does_not_apply_conditions_to_collection_for_admin_api_section(
        SectionProviderInterface $sectionProvider,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        AdminApiSection $section,
    ): void {
        $sectionProvider->getSection()->willReturn($section);
        $queryBuilder->getRootAliases()->shouldNotBeCalled();

        $this->applyToCollection($queryBuilder, $queryNameGenerator, OrderInterface::class, new Get());
    }

    function it_applies_conditions_to_collection(
        SectionProviderInterface $sectionProvider,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        ShopApiSection $section,
        ShopUserInterface $user,
        CustomerInterface $customer,
        Expr $expr,
        Expr\Func $exprNeq,
    ): void {
        $user->getCustomer()->willReturn($customer);
        $sectionProvider->getSection()->willReturn($section);

        $queryBuilder->getRootAliases()->willReturn(['o']);
        $queryNameGenerator->generateParameterName('state')->willReturn('state');

        $queryBuilder->expr()->willReturn($expr);
        $expr->neq('o.state', ':state')->willReturn($exprNeq);
        $queryBuilder->andWhere($exprNeq)->shouldBeCalled()->willReturn($queryBuilder->getWrappedObject());
        $queryBuilder->setParameter('state', OrderInterface::STATE_CART)->shouldBeCalled()->willReturn($queryBuilder->getWrappedObject());

        $this->applyToCollection($queryBuilder, $queryNameGenerator, OrderInterface::class, new Get());
    }

    function it_does_not_apply_conditions_to_item_for_unsupported_resource(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $queryBuilder->getRootAliases()->shouldNotBeCalled();

        $this->applyToItem($queryBuilder, $queryNameGenerator, ResourceInterface::class, [], new Get());
    }

    function it_does_not_apply_conditions_to_item_for_admin_api_section(
        SectionProviderInterface $sectionProvider,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        AdminApiSection $section,
    ): void {
        $sectionProvider->getSection()->willReturn($section);
        $queryBuilder->getRootAliases()->shouldNotBeCalled();

        $this->applyToItem($queryBuilder, $queryNameGenerator, OrderInterface::class, [], new Get());
    }

    function it_does_not_apply_conditions_to_item_if_operation_is_allowed(
        SectionProviderInterface $sectionProvider,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        ShopApiSection $section,
        Operation $operation,
    ): void {
        $sectionProvider->getSection()->willReturn($section);
        $operation->getName()->willReturn('_api_/shop/orders/{tokenValue}/payments/{paymentId}/configuration_get');
        $queryBuilder->getRootAliases()->shouldNotBeCalled();

        $this->applyToItem($queryBuilder, $queryNameGenerator, OrderInterface::class, [], $operation);
    }

    function it_applies_conditions_to_item(
        SectionProviderInterface $sectionProvider,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        ShopApiSection $section,
        CustomerInterface $customer,
        Operation $operation,
        Expr $expr,
        Expr\Func $exprEq,
    ): void {
        $sectionProvider->getSection()->willReturn($section);
        $operation->getName()->willReturn('_api_/shop/orders/{tokenValue}/new_get');

        $queryBuilder->getRootAliases()->willReturn(['o']);
        $queryNameGenerator->generateParameterName('state')->willReturn('state');

        $queryBuilder->expr()->willReturn($expr);
        $expr->eq('o.state', ':state')->willReturn($exprEq);
        $queryBuilder->andWhere($exprEq)->shouldBeCalled()->willReturn($queryBuilder->getWrappedObject());
        $queryBuilder->setParameter('state', OrderInterface::STATE_CART)->shouldBeCalled()->willReturn($queryBuilder->getWrappedObject());

        $this->applyToItem($queryBuilder, $queryNameGenerator, OrderInterface::class, [], $operation);
    }
}
