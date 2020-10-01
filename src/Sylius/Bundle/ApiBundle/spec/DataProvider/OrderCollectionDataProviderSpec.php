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

namespace spec\Sylius\Bundle\ApiBundle\DataProvider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

final class OrderCollectionDataProviderSpec extends ObjectBehavior
{
    function let(UserContextInterface $userContext, OrderRepositoryInterface $orderRepository): void
    {
        $this->beConstructedWith($userContext, $orderRepository);
    }

    function it_supports_only_orders(): void
    {
        $this->supports(OrderInterface::class, 'get')->shouldReturn(true);
        $this->supports(ProductInterface::class, 'get')->shouldReturn(false);
    }

    function it_provides_all_orders_for_admin_user(
        UserContextInterface $userContext,
        AdminUserInterface $user,
        OrderRepositoryInterface $orderRepository,
        OrderInterface $firstOrder,
        OrderInterface $secondOrder
    ): void {
        $userContext->getUser()->willReturn($user);
        $user->getRoles()->willReturn(['ROLE_API_ACCESS']);
        $orderRepository->findAllExceptCarts()->willReturn([$firstOrder, $secondOrder]);

        $this->getCollection(OrderInterface::class)->shouldReturn([$firstOrder, $secondOrder]);
    }

    function it_provides_orders_created_by_logged_in_user(
        UserContextInterface $userContext,
        ShopUserInterface $user,
        CustomerInterface $customer,
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order
    ): void {
        $userContext->getUser()->willReturn($user);
        $user->getCustomer()->willReturn($customer);
        $orderRepository->findByCustomer($customer)->willReturn([$order]);

        $this->getCollection(OrderInterface::class)->shouldReturn([$order]);
    }
}
