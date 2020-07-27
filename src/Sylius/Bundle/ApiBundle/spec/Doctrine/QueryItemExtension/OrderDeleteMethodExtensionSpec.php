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

namespace spec\Sylius\Bundle\ApiBundle\Doctrine\QueryItemExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Helper\UserContextHelperInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final class OrderDeleteMethodExtensionSpec extends ObjectBehavior
{
    function let(UserContextHelperInterface $userContextHelper): void
    {
        $this->beConstructedWith($userContextHelper);
    }

    function it_applies_conditions_to_delete_order_with_state_cart_and_with_null_user_if_present_user_is_null(
        UserContextHelperInterface $userContextHelper,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator
    ): void {
        $queryBuilder->getRootAliases()->willReturn(['o']);

        $userContextHelper->getUser()->willReturn(null);
        $userContextHelper->isVisitor()->willReturn(true);

        $queryBuilder
            ->andWhere(sprintf('%s.customer IS NULL', 'o'))
            ->shouldBeCalled()
            ->willReturn($queryBuilder)
        ;

        $queryBuilder
            ->andWhere(sprintf('%s.state = :state', 'o'))
            ->shouldBeCalled()
            ->willReturn($queryBuilder)
        ;

        $queryBuilder
            ->setParameter('state', OrderInterface::STATE_CART)
            ->shouldBeCalled()
            ->willReturn($queryBuilder)
        ;

        $this->applyToItem(
            $queryBuilder,
            $queryNameGenerator,
            OrderInterface::class,
            ['tokenValue' => 'xaza-tt_fee'],
            Request::METHOD_DELETE,
            []
        );
    }

    function it_applies_conditions_to_delete_order_with_state_cart_by_authorized_shop_user_that_is_assigns_to_this_order(
        UserContextHelperInterface $userContextHelper,
        QueryBuilder $queryBuilder,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        QueryNameGeneratorInterface $queryNameGenerator
    ): void {
        $queryBuilder->getRootAliases()->willReturn(['o']);

        $userContextHelper->getUser()->willReturn($shopUser);
        $userContextHelper->isVisitor()->willReturn(false);
        $userContextHelper->hasShopUserRoleApiAccess()->willReturn(true);

        $shopUser->getCustomer()->willReturn($customer);
        $customer->getId()->willReturn(1);
        $shopUser->getRoles()->willReturn(['ROLE_API_ACCESS']);

        $queryBuilder
            ->andWhere(sprintf('%s.customer = :customer', 'o'))
            ->shouldBeCalled()
            ->willReturn($queryBuilder)
        ;
        $queryBuilder
            ->setParameter('customer', 1)
            ->shouldBeCalled()
            ->willReturn($queryBuilder)
        ;

        $queryBuilder
            ->andWhere(sprintf('%s.state = :state', 'o'))
            ->shouldBeCalled()
            ->willReturn($queryBuilder)
        ;

        $queryBuilder
            ->setParameter('state', OrderInterface::STATE_CART)
            ->shouldBeCalled()
            ->willReturn($queryBuilder)
        ;

        $this->applyToItem(
            $queryBuilder,
            $queryNameGenerator,
            OrderInterface::class,
            ['tokenValue' => 'xaza-tt_fee'],
            Request::METHOD_DELETE,
            []
        );
    }

    function it_throws_an_exception_when_unauthorized_shop_user_try_to_delete_order_with_state_cart(
        UserContextHelperInterface $userContextHelper,
        QueryBuilder $queryBuilder,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        QueryNameGeneratorInterface $queryNameGenerator
    ): void {
        $queryBuilder->getRootAliases()->willReturn(['o']);

        $userContextHelper->getUser()->willReturn($shopUser);
        $userContextHelper->isVisitor()->willReturn(false);
        $userContextHelper->hasShopUserRoleApiAccess()->willReturn(false);
        $userContextHelper->hasAdminRoleApiAccess()->willReturn(false);

        $shopUser->getCustomer()->willReturn($customer);
        $customer->getId()->willReturn(1);
        $shopUser->getRoles()->willReturn([]);

        $this
            ->shouldThrow(AccessDeniedHttpException::class)
            ->during(
                'applyToItem',
                [
                    $queryBuilder,
                    $queryNameGenerator,
                    OrderInterface::class,
                    ['tokenValue' => 'xaza-tt_fee'],
                    Request::METHOD_DELETE,
                    [],
                ]
            )
        ;
    }

    function it_applies_conditions_to_delete_order_with_state_cart_by_authorized_admin_user(
        UserContextHelperInterface $userContextHelper,
        QueryBuilder $queryBuilder,
        AdminUserInterface $adminUser,
        QueryNameGeneratorInterface $queryNameGenerator
    ): void {
        $queryBuilder->getRootAliases()->willReturn(['o']);

        $userContextHelper->getUser()->willReturn($adminUser);
        $userContextHelper->isVisitor()->willReturn(false);
        $userContextHelper->hasShopUserRoleApiAccess()->willReturn(false);
        $userContextHelper->hasAdminRoleApiAccess()->willReturn(true);

        $adminUser->getRoles()->willReturn(['ROLE_API_ACCESS']);

        $queryBuilder
            ->andWhere(sprintf('%s.state = :state', 'o'))
            ->shouldBeCalled()
            ->willReturn($queryBuilder)
        ;

        $queryBuilder
            ->setParameter('state', OrderInterface::STATE_CART)
            ->shouldBeCalled()
            ->willReturn($queryBuilder)
        ;

        $this->applyToItem(
            $queryBuilder,
            $queryNameGenerator,
            OrderInterface::class,
            ['tokenValue' => 'xaza-tt_fee'],
            Request::METHOD_DELETE,
            []
        );
    }

    function it_throws_an_exception_when_unauthorized_admin_user_try_to_delete_order_with_state_cart(
        UserContextHelperInterface $userContextHelper,
        QueryBuilder $queryBuilder,
        AdminUserInterface $adminUser,
        QueryNameGeneratorInterface $queryNameGenerator
    ): void {
        $queryBuilder->getRootAliases()->willReturn(['o']);

        $userContextHelper->getUser()->willReturn($adminUser);
        $userContextHelper->isVisitor()->willReturn(false);
        $userContextHelper->hasShopUserRoleApiAccess()->willReturn(false);
        $userContextHelper->hasAdminRoleApiAccess()->willReturn(false);

        $adminUser->getRoles()->willReturn([]);

        $this
            ->shouldThrow(AccessDeniedHttpException::class)
            ->during(
                'applyToItem',
                [
                    $queryBuilder,
                    $queryNameGenerator,
                    OrderInterface::class,
                    ['tokenValue' => 'xaza-tt_fee'],
                    Request::METHOD_DELETE,
                    [],
                ]
            )
        ;
    }
}
