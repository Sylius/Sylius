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
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderItemUnitRepositoryInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

final class OrderItemUnitItemDataProviderSpec extends ObjectBehavior
{
    function let(OrderItemUnitRepositoryInterface $orderItemUnitRepository, UserContextInterface $userContext): void
    {
        $this->beConstructedWith($orderItemUnitRepository, $userContext);
    }

    function it_supports_only_order_item_unit(): void
    {
        $this->supports(OrderItemUnitInterface::class, 'get')->shouldReturn(true);
        $this->supports(ResourceInterface::class, 'get')->shouldReturn(false);
    }

    function it_provides_order_item_unit_for_shop_user(
        OrderItemUnitRepositoryInterface $orderItemUnitRepository,
        UserContextInterface $userContext,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        OrderItemUnitInterface $orderItemUnit,
    ) {
        $userContext->getUser()->willReturn($shopUser);

        $shopUser->getCustomer()->willReturn($customer);
        $shopUser->getRoles()->willReturn(['ROLE_USER']);

        $orderItemUnitRepository
            ->findOneByCustomer('123', $customer->getWrappedObject())
            ->willReturn($orderItemUnit)
        ;

        $this->getItem(OrderItemUnitInterface::class, '123')->shouldReturn($orderItemUnit);
    }

    function it_provides_order_item_unit_for_admin_user(
        OrderItemUnitRepositoryInterface $orderItemUnitRepository,
        UserContextInterface $userContext,
        AdminUserInterface $adminUser,
        OrderItemUnitInterface $orderItemUnit,
    ) {
        $userContext->getUser()->willReturn($adminUser);

        $adminUser->getRoles()->willReturn(['ROLE_API_ACCESS']);

        $orderItemUnitRepository->find('123')->willReturn($orderItemUnit);

        $this->getItem(OrderItemUnitInterface::class, '123')->shouldReturn($orderItemUnit);
    }

    function it_returns_null_if_shop_user_has_no_customer(
        OrderItemUnitRepositoryInterface $orderItemUnitRepository,
        UserContextInterface $userContext,
        ShopUserInterface $shopUser,
    ) {
        $userContext->getUser()->willReturn($shopUser);

        $shopUser->getCustomer()->willReturn(null);
        $shopUser->getRoles()->willReturn(['ROLE_USER']);

        $orderItemUnitRepository->findOneByCustomer('123', new Customer())->shouldNotBeCalled();

        $this->getItem(OrderItemUnitInterface::class, '123')->shouldReturn(null);
    }

    function it_returns_null_if_shop_user_does_have_proper_roles(
        OrderItemUnitRepositoryInterface $orderItemUnitRepository,
        UserContextInterface $userContext,
        CustomerInterface $customer,
        ShopUserInterface $shopUser,
    ) {
        $userContext->getUser()->willReturn($shopUser);

        $shopUser->getCustomer()->willReturn($customer);
        $shopUser->getRoles()->willReturn(['']);

        $orderItemUnitRepository->findOneByCustomer('123', new Customer())->shouldNotBeCalled();

        $this->getItem(OrderItemUnitInterface::class, '123')->shouldReturn(null);
    }
}
