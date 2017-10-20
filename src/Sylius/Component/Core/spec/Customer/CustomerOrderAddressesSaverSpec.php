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

namespace spec\Sylius\Component\Core\Customer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Customer\CustomerAddressAdderInterface;
use Sylius\Component\Core\Customer\OrderAddressesSaverInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

final class CustomerOrderAddressesSaverSpec extends ObjectBehavior
{
    function let(CustomerAddressAdderInterface $addressAdder): void
    {
        $this->beConstructedWith($addressAdder);
    }

    function it_implements_order_addresses_saver_interface(): void
    {
        $this->shouldImplement(OrderAddressesSaverInterface::class);
    }

    function it_saves_addresses_from_given_order(
        CustomerAddressAdderInterface $addressAdder,
        OrderInterface $order,
        CustomerInterface $customer,
        ShopUserInterface $user,
        AddressInterface $shippingAddress,
        AddressInterface $billingAddress
    ): void {
        $order->getCustomer()->willReturn($customer);
        $customer->getUser()->willReturn($user);

        $order->getShippingAddress()->willReturn($shippingAddress);
        $order->getBillingAddress()->willReturn($billingAddress);

        $addressAdder->add($customer, clone $shippingAddress)->shouldBeCalled();
        $addressAdder->add($customer, clone $billingAddress)->shouldBeCalled();

        $this->saveAddresses($order);
    }

    function it_does_not_save_addresses_for_guest_order(
        CustomerAddressAdderInterface $addressAdder,
        OrderInterface $order,
        CustomerInterface $customer
    ): void {
        $order->getCustomer()->willReturn($customer);
        $customer->getUser()->willReturn(null);

        $addressAdder->add($customer, Argument::any())->shouldNotBeCalled();
        $addressAdder->add($customer, Argument::any())->shouldNotBeCalled();

        $this->saveAddresses($order);
    }
}
