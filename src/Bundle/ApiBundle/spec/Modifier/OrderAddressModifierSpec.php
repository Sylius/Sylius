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

namespace spec\Sylius\Bundle\ApiBundle\Modifier;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\ApiBundle\Mapper\AddressMapperInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;

final class OrderAddressModifierSpec extends ObjectBehavior
{
    function let(
        StateMachineInterface $stateMachine,
        AddressMapperInterface $addressMapper,
    ) {
        $this->beConstructedWith($stateMachine, $addressMapper);
    }

    function it_modifies_addresses_of_an_order_without_provided_shipping_address(
        StateMachineInterface $stateMachine,
        AddressInterface $billingAddress,
        OrderInterface $order,
        ChannelInterface $channel,
    ): void {
        $order->getTokenValue()->willReturn('ORDERTOKEN');
        $order->getShippingAddress()->willReturn(null);
        $order->getBillingAddress()->willReturn(null);
        $order->getChannel()->willReturn($channel);

        $channel->isShippingAddressInCheckoutRequired()->willReturn(false);

        $order->setBillingAddress($billingAddress)->shouldBeCalled();
        $order->setShippingAddress(Argument::type(AddressInterface::class))->shouldBeCalled();

        $stateMachine
            ->can($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_ADDRESS)
            ->willReturn(true)
        ;
        $stateMachine
            ->apply($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_ADDRESS)
            ->shouldBeCalled()
        ;

        $this->modify($order, $billingAddress->getWrappedObject(), null);
    }

    function it_modifies_addresses_of_an_order_without_provided_billing_address(
        StateMachineInterface $stateMachine,
        AddressInterface $shippingAddress,
        OrderInterface $order,
        ChannelInterface $channel,
    ): void {
        $order->getTokenValue()->willReturn('ORDERTOKEN');
        $order->getShippingAddress()->willReturn(null);
        $order->getBillingAddress()->willReturn(null);
        $order->getChannel()->willReturn($channel);

        $channel->isShippingAddressInCheckoutRequired()->willReturn(true);

        $order->setShippingAddress($shippingAddress)->shouldBeCalled();
        $order->setBillingAddress(Argument::type(AddressInterface::class))->shouldBeCalled();

        $stateMachine
            ->can($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_ADDRESS)
            ->willReturn(true)
        ;
        $stateMachine
            ->apply($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_ADDRESS)
            ->shouldBeCalled()
        ;

        $this->modify($order, null, $shippingAddress->getWrappedObject());
    }

    function it_modifies_addresses_of_an_order(
        StateMachineInterface $stateMachine,
        AddressInterface $billingAddress,
        AddressInterface $shippingAddress,
        OrderInterface $order,
        ChannelInterface $channel,
    ): void {
        $order->getTokenValue()->willReturn('ORDERTOKEN');
        $order->getShippingAddress()->willReturn(null);
        $order->getBillingAddress()->willReturn(null);
        $order->getChannel()->willReturn($channel);

        $channel->isShippingAddressInCheckoutRequired()->willReturn(false);

        $order->setBillingAddress($billingAddress)->shouldBeCalled();
        $order->setShippingAddress($shippingAddress)->shouldBeCalled();

        $stateMachine
            ->can($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_ADDRESS)
            ->willReturn(true)
        ;
        $stateMachine
            ->apply($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_ADDRESS)
            ->shouldBeCalled()
        ;

        $this->modify($order, $billingAddress->getWrappedObject(), $shippingAddress->getWrappedObject());
    }

    function it_updates_order_addresses(
        StateMachineInterface $stateMachine,
        AddressMapperInterface $addressMapper,
        AddressInterface $newBillingAddress,
        AddressInterface $newShippingAddress,
        AddressInterface $oldBillingAddress,
        AddressInterface $oldShippingAddress,
        OrderInterface $order,
        ChannelInterface $channel,
    ): void {
        $order->getTokenValue()->willReturn('ORDERTOKEN');
        $order->getBillingAddress()->willReturn($oldBillingAddress);
        $order->getShippingAddress()->willReturn($oldShippingAddress);
        $order->getChannel()->willReturn($channel);

        $channel->isShippingAddressInCheckoutRequired()->willReturn(false);

        $addressMapper->mapExisting($oldBillingAddress, $newBillingAddress)->willReturn($oldBillingAddress);
        $addressMapper->mapExisting($oldShippingAddress, $newShippingAddress)->willReturn($oldShippingAddress);

        $order->setBillingAddress($oldBillingAddress)->shouldBeCalled();
        $order->setShippingAddress($oldShippingAddress)->shouldBeCalled();

        $stateMachine
            ->can($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_ADDRESS)
            ->willReturn(true)
        ;
        $stateMachine
            ->apply($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_ADDRESS)
            ->shouldBeCalled()
        ;

        $this->modify($order, $newBillingAddress->getWrappedObject(), $newShippingAddress->getWrappedObject());
    }

    function it_throws_an_exception_if_order_cannot_be_addressed(
        StateMachineInterface $stateMachine,
        AddressInterface $billingAddress,
        AddressInterface $shippingAddress,
        OrderInterface $order,
    ): void {
        $stateMachine
            ->can($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_ADDRESS)
            ->willReturn(false)
        ;

        $this
            ->shouldThrow(\LogicException::class)
            ->during('modify', [$order, $billingAddress->getWrappedObject(), $shippingAddress->getWrappedObject()])
        ;
    }
}
