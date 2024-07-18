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

namespace spec\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\OrderItem;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

final class ShopUserBasedExtensionSpec extends ObjectBehavior
{
    function let(UserContextInterface $userContext): void
    {
        $this->beConstructedWith($userContext);
    }

    function it_does_not_apply_to_non_order_item_resources(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $this->applyToCollection($queryBuilder, $queryNameGenerator, \stdClass::class);
        $queryBuilder->leftJoin(Argument::any(), Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_does_not_apply_if_user_is_not_logged_in(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $userContext->getUser()->willReturn(null);

        $this->applyToCollection($queryBuilder, $queryNameGenerator, OrderItemInterface::class);
        $queryBuilder->leftJoin(Argument::any(), Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_does_not_apply_if_user_is_admin_user(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        AdminUserInterface $adminUser,
    ): void {
        $userContext->getUser()->willReturn($adminUser);

        $this->applyToCollection($queryBuilder, $queryNameGenerator, OrderItemInterface::class);
        $queryBuilder->leftJoin(Argument::any(), Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_applies_filters_for_shop_users(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
    ): void {
        $userContext->getUser()->willReturn($shopUser);
        $shopUser->getCustomer()->willReturn($customer);
        $customer->getId()->willReturn(42);

        $queryBuilder->getRootAliases()->willReturn(['o']);
        $queryNameGenerator->generateParameterName('order')->shouldBeCalled()->willReturn('order');
        $queryNameGenerator->generateJoinAlias('customer_join')->shouldBeCalled()->willReturn('customer_join');
        $queryNameGenerator->generateParameterName('customer')->shouldBeCalled()->willReturn('customer');

        $queryBuilder->leftJoin('o.order', 'order')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->leftJoin('order.customer', 'customer_join')->shouldBeCalled()->willReturn($queryBuilder);

        $queryBuilder
            ->andWhere('customer_join = :customer')
            ->shouldBeCalled()
            ->willReturn($queryBuilder)
        ;

        $queryBuilder
            ->setParameter('customer', 42)
            ->shouldBeCalled()
            ->willReturn($queryBuilder)
        ;

        $queryBuilder->addOrderBy('o.id', 'ASC')->willReturn($queryBuilder);

        $this->applyToCollection($queryBuilder, $queryNameGenerator, OrderItemInterface::class);
    }
}
