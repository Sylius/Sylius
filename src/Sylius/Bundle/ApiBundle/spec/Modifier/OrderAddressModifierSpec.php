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

namespace spec\Sylius\Bundle\ApiBundle\Modifier;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Mapper\AddressMapperInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class OrderAddressModifierSpec extends ObjectBehavior
{
    function let(AddressMapperInterface $addressMapper)
    {
        $this->beConstructedWith($addressMapper);
    }

    function it_handles_addressing_an_order_without_provided_shipping_address(
        AddressInterface $billingAddress,
        OrderInterface $order,
    ): void {
        $order->getTokenValue()->willReturn('ORDERTOKEN');

        $order->getShippingAddress()->willReturn(null);
        $order->getBillingAddress()->willReturn(null);

        $order->setBillingAddress($billingAddress)->shouldBeCalled();
        $order->setShippingAddress(Argument::type(AddressInterface::class))->shouldBeCalled();

        $this->modify($order, $billingAddress->getWrappedObject(), null);
    }

    function it_handles_addressing_an_order_for_visitor(
        AddressInterface $billingAddress,
        AddressInterface $shippingAddress,
        OrderInterface $order,
    ): void {
        $order->getTokenValue()->willReturn('ORDERTOKEN');

        $order->getShippingAddress()->willReturn(null);
        $order->getBillingAddress()->willReturn(null);
        $order->setBillingAddress($billingAddress)->shouldBeCalled();
        $order->setShippingAddress($shippingAddress)->shouldBeCalled();

        $this->modify($order, $billingAddress->getWrappedObject(), $shippingAddress->getWrappedObject());
    }

    function it_updates_order_address_based_on_data_form_new_order_address(
        AddressMapperInterface $addressMapper,
        AddressInterface $newBillingAddress,
        AddressInterface $newShippingAddress,
        AddressInterface $oldBillingAddress,
        AddressInterface $oldShippingAddress,
        OrderInterface $order,
    ): void {
        $order->getTokenValue()->willReturn('ORDERTOKEN');

        $order->getBillingAddress()->willReturn($oldBillingAddress);
        $order->getShippingAddress()->willReturn($oldShippingAddress);

        $addressMapper->mapExisting($oldBillingAddress, $newBillingAddress)->willReturn($oldBillingAddress);
        $addressMapper->mapExisting($oldShippingAddress, $newShippingAddress)->willReturn($oldShippingAddress);

        $order->setBillingAddress($oldBillingAddress)->shouldBeCalled();
        $order->setShippingAddress($oldShippingAddress)->shouldBeCalled();

        $this->modify($order, $newBillingAddress->getWrappedObject(), $newShippingAddress->getWrappedObject());
    }
}
