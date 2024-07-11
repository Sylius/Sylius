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

namespace spec\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Put;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\HttpFoundation\Request;

final class OrderShopUserItemExtensionSpec extends ObjectBehavior
{
    function let(UserContextInterface $userContext): void
    {
        $this->beConstructedWith($userContext, ['shop_select_payment_method', 'shop_account_change_payment_method']);
    }

    function it_filters_carts_for_shop_users_to_the_one_owned_by_them_for_methods_other_than_get(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $queryBuilder->getRootAliases()->willReturn(['o']);

        $userContext->getUser()->willReturn($shopUser);

        $shopUser->getCustomer()->willReturn($customer);
        $customer->getId()->willReturn(1);

        $queryNameGenerator->generateParameterName('state')->shouldBeCalled()->willReturn('state');
        $queryNameGenerator->generateParameterName('customer')->shouldBeCalled()->willReturn('customer');

        $queryBuilder->andWhere('o.customer = :customer')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setParameter('customer', 1)->shouldBeCalled()->willReturn($queryBuilder);

        $queryBuilder->andWhere('o.state = :state')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setParameter('state', OrderInterface::STATE_CART)->shouldBeCalled()->willReturn($queryBuilder);

        $this->applyToItem(
            $queryBuilder,
            $queryNameGenerator,
            OrderInterface::class,
            ['tokenValue' => 'xaza-tt_fee'],
            new Put(),
            [ContextKeys::HTTP_REQUEST_METHOD_TYPE => Request::METHOD_POST],
        );

        $this->applyToItem(
            $queryBuilder,
            $queryNameGenerator,
            OrderInterface::class,
            ['tokenValue' => 'xaza-tt_fee'],
            new Put(),
            [ContextKeys::HTTP_REQUEST_METHOD_TYPE => Request::METHOD_PATCH],
        );

        $this->applyToItem(
            $queryBuilder,
            $queryNameGenerator,
            OrderInterface::class,
            ['tokenValue' => 'xaza-tt_fee'],
            new Put(),
            [ContextKeys::HTTP_REQUEST_METHOD_TYPE => Request::METHOD_PUT],
        );

        $this->applyToItem(
            $queryBuilder,
            $queryNameGenerator,
            OrderInterface::class,
            ['tokenValue' => 'xaza-tt_fee'],
            new Put(),
            [ContextKeys::HTTP_REQUEST_METHOD_TYPE => Request::METHOD_DELETE],
        );
    }

    function it_filters_carts_and_orders_for_shop_users_to_the_one_owned_by_them_for_get_and_payment_selection_operations(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $queryBuilder->getRootAliases()->willReturn(['o']);

        $userContext->getUser()->willReturn($shopUser);

        $shopUser->getCustomer()->willReturn($customer);
        $customer->getId()->willReturn(1);

        $queryNameGenerator->generateParameterName('customer')->shouldBeCalled()->willReturn('customer');

        $queryBuilder->andWhere('o.customer = :customer')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setParameter('customer', 1)->shouldBeCalled()->willReturn($queryBuilder);

        $this->applyToItem(
            $queryBuilder,
            $queryNameGenerator,
            OrderInterface::class,
            ['tokenValue' => 'xaza-tt_fee'],
            new Put(),
            [ContextKeys::HTTP_REQUEST_METHOD_TYPE => Request::METHOD_GET],
        );

        $this->applyToItem(
            $queryBuilder,
            $queryNameGenerator,
            OrderInterface::class,
            ['tokenValue' => 'xaza-tt_fee'],
            new Put(name: 'shop_select_payment_method'),
            [ContextKeys::HTTP_REQUEST_METHOD_TYPE => Request::METHOD_PATCH],
        );

        $this->applyToItem(
            $queryBuilder,
            $queryNameGenerator,
            OrderInterface::class,
            ['tokenValue' => 'xaza-tt_fee'],
            new Put(name: 'shop_account_change_payment_method'),
            [ContextKeys::HTTP_REQUEST_METHOD_TYPE => Request::METHOD_PATCH],
        );
    }

    function it_does_nothing_if_logged_in_user_is_not_shop_user(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        UserInterface $user,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $queryBuilder->getRootAliases()->willReturn(['o']);

        $userContext->getUser()->willReturn(null, $user);

        $queryBuilder->leftJoin(Argument::any())->shouldNotBeCalled();
        $queryBuilder->expr()->shouldNotBeCalled();
        $queryBuilder->setParameter(Argument::any())->shouldNotBeCalled();
        $queryBuilder->andWhere(Argument::any())->shouldNotBeCalled();

        $this->applyToItem(
            $queryBuilder,
            $queryNameGenerator,
            OrderInterface::class,
            ['tokenValue' => 'xaza-tt_fee'],
            new Put(),
            [ContextKeys::HTTP_REQUEST_METHOD_TYPE => Request::METHOD_PUT],
        );

        $this->applyToItem(
            $queryBuilder,
            $queryNameGenerator,
            OrderInterface::class,
            ['tokenValue' => 'xaza-tt_fee'],
            new Put(),
            [ContextKeys::HTTP_REQUEST_METHOD_TYPE => Request::METHOD_PUT],
        );
    }

    function it_does_nothing_if_object_passed_is_different_than_order(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $queryBuilder->leftJoin(Argument::any())->shouldNotBeCalled();
        $queryBuilder->expr()->shouldNotBeCalled();
        $queryBuilder->setParameter(Argument::any())->shouldNotBeCalled();
        $queryBuilder->andWhere(Argument::any())->shouldNotBeCalled();

        $this->applyToItem(
            $queryBuilder,
            $queryNameGenerator,
            \stdClass::class,
            ['tokenValue' => 'xaza-tt_fee'],
            new Put(),
            [ContextKeys::HTTP_REQUEST_METHOD_TYPE => Request::METHOD_PUT],
        );
    }
}
