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

namespace spec\Sylius\Bundle\ApiBundle\DataProvider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderItemRepositoryInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

final class OrderItemItemDataProviderSpec extends ObjectBehavior
{
    function let(OrderItemRepositoryInterface $orderItemRepository, UserContextInterface $userContext): void
    {
        $this->beConstructedWith($orderItemRepository, $userContext);
    }

    function it_supports_only_order_item(): void
    {
        $this->supports(OrderItemInterface::class, 'get')->shouldReturn(true);
        $this->supports(ResourceInterface::class, 'get')->shouldReturn(false);
    }

    function it_provides_order_item_for_shop_user(
        OrderItemRepositoryInterface $orderItemRepository,
        UserContextInterface $userContext,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        OrderItemInterface $orderItem,
    ) {
        $userContext->getUser()->willReturn($shopUser);

        $shopUser->getCustomer()->willReturn($customer);
        $shopUser->getRoles()->willReturn(['ROLE_USER']);

        $orderItemRepository
            ->findOneByIdAndCustomer('123', $customer->getWrappedObject())
            ->willReturn($orderItem)
        ;

        $this->getItem(OrderItemInterface::class, '123')->shouldReturn($orderItem);
    }

    function it_provides_order_item_for_admin_user(
        OrderItemRepositoryInterface $orderItemRepository,
        UserContextInterface $userContext,
        AdminUserInterface $adminUser,
        OrderItemInterface $orderItem,
    ) {
        $userContext->getUser()->willReturn($adminUser);

        $adminUser->getRoles()->willReturn(['ROLE_API_ACCESS']);

        $orderItemRepository->find('123')->willReturn($orderItem);

        $this->getItem(OrderItemInterface::class, '123')->shouldReturn($orderItem);
    }

    function it_returns_null_if_shop_user_has_no_customer(
        OrderItemRepositoryInterface $orderItemRepository,
        UserContextInterface $userContext,
        ShopUserInterface $shopUser,
    ) {
        $userContext->getUser()->willReturn($shopUser);

        $shopUser->getCustomer()->willReturn(null);
        $shopUser->getRoles()->willReturn(['ROLE_USER']);

        $orderItemRepository->findOneByIdAndCustomer('123', new Customer())->shouldNotBeCalled();

        $this->getItem(OrderItemInterface::class, '123')->shouldReturn(null);
    }

    function it_returns_null_if_shop_user_does_have_proper_roles(
        OrderItemRepositoryInterface $orderItemRepository,
        UserContextInterface $userContext,
        CustomerInterface $customer,
        ShopUserInterface $shopUser,
    ) {
        $userContext->getUser()->willReturn($shopUser);

        $shopUser->getCustomer()->willReturn($customer);
        $shopUser->getRoles()->willReturn(['']);

        $orderItemRepository->findOneByIdAndCustomer('123', new Customer())->shouldNotBeCalled();

        $this->getItem(OrderItemInterface::class, '123')->shouldReturn(null);
    }
}
