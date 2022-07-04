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
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Bundle\ApiBundle\Mapper\AddressMapperInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;

final class OrderAddressModifierSpec extends ObjectBehavior
{
    function let(
        StateMachineFactoryInterface $stateMachineFactory,
        AddressMapperInterface $addressMapper,
    ) {
        $this->beConstructedWith($stateMachineFactory, $addressMapper);
    }

    function it_handles_addressing_an_order_without_provided_shipping_address(
        StateMachineFactoryInterface $stateMachineFactory,
        AddressInterface $billingAddress,
        OrderInterface $order,
        StateMachineInterface $stateMachine,
    ): void {
        $order->getTokenValue()->willReturn('ORDERTOKEN');

        $order->getShippingAddress()->willReturn(null);
        $order->getBillingAddress()->willReturn(null);

        $order->setBillingAddress($billingAddress)->shouldBeCalled();
        $order->setShippingAddress(Argument::type(AddressInterface::class))->shouldBeCalled();

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_ADDRESS)->willReturn(true);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_ADDRESS)->shouldBeCalled();

        $this->modify($order, $billingAddress->getWrappedObject(), null, 'r2d2@droid.com');
    }

    function it_handles_addressing_an_order_for_visitor(
        StateMachineFactoryInterface $stateMachineFactory,
        AddressInterface $billingAddress,
        AddressInterface $shippingAddress,
        OrderInterface $order,
        StateMachineInterface $stateMachine,
    ): void {
        $order->getTokenValue()->willReturn('ORDERTOKEN');

        $order->getShippingAddress()->willReturn(null);
        $order->getBillingAddress()->willReturn(null);
        $order->setBillingAddress($billingAddress)->shouldBeCalled();
        $order->setShippingAddress($shippingAddress)->shouldBeCalled();

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_ADDRESS)->willReturn(true);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_ADDRESS)->shouldBeCalled();

        $this->modify($order, $billingAddress->getWrappedObject(), $shippingAddress->getWrappedObject(), 'r2d2@droid.com');
    }

    function it_updates_order_address_based_on_data_form_new_order_address(
        StateMachineFactoryInterface $stateMachineFactory,
        AddressMapperInterface $addressMapper,
        AddressInterface $newBillingAddress,
        AddressInterface $newShippingAddress,
        AddressInterface $oldBillingAddress,
        AddressInterface $oldShippingAddress,
        OrderInterface $order,
        StateMachineInterface $stateMachine,
    ): void {
        $order->getTokenValue()->willReturn('ORDERTOKEN');

        $order->getBillingAddress()->willReturn($oldBillingAddress);
        $order->getShippingAddress()->willReturn($oldShippingAddress);

        $addressMapper->mapExisting($oldBillingAddress, $newBillingAddress)->willReturn($oldBillingAddress);
        $addressMapper->mapExisting($oldShippingAddress, $newShippingAddress)->willReturn($oldShippingAddress);

        $order->setBillingAddress($oldBillingAddress)->shouldBeCalled();
        $order->setShippingAddress($oldShippingAddress)->shouldBeCalled();

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_ADDRESS)->willReturn(true);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_ADDRESS)->shouldBeCalled();

        $this->modify($order, $newBillingAddress->getWrappedObject(), $newShippingAddress->getWrappedObject(), 'r2d2@droid.com');
    }

    function it_throws_an_exception_if_order_cannot_be_addressed(
        StateMachineFactoryInterface $stateMachineFactory,
        AddressInterface $billingAddress,
        AddressInterface $shippingAddress,
        OrderInterface $order,
        StateMachineInterface $stateMachine,
    ): void {
        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_ADDRESS)->willReturn(false);

        $this->shouldThrow(\LogicException::class)->during('modify', [$order, $billingAddress->getWrappedObject(), $shippingAddress->getWrappedObject(), 'r2d2@droid.com']);
    }
}
