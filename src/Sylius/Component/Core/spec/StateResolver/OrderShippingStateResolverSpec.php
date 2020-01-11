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

namespace spec\Sylius\Component\Core\StateResolver;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\OrderShippingStates;
use Sylius\Component\Core\OrderShippingTransitions;
use Sylius\Component\Order\StateResolver\StateResolverInterface;

final class OrderShippingStateResolverSpec extends ObjectBehavior
{
    function let(FactoryInterface $stateMachineFactory): void
    {
        $this->beConstructedWith($stateMachineFactory);
    }

    function it_implements_an_order_state_resolver_interface(): void
    {
        $this->shouldImplement(StateResolverInterface::class);
    }

    function it_marks_an_order_as_shipped_if_all_shipments_delivered(
        FactoryInterface $stateMachineFactory,
        OrderInterface $order,
        ShipmentInterface $shipment1,
        ShipmentInterface $shipment2,
        StateMachineInterface $orderStateMachine
    ): void {
        $shipments = new ArrayCollection();
        $shipments->add($shipment1->getWrappedObject());
        $shipments->add($shipment2->getWrappedObject());

        $order->getShipments()->willReturn($shipments);
        $order->getShippingState()->willReturn(OrderShippingStates::STATE_READY);
        $stateMachineFactory->get($order, OrderShippingTransitions::GRAPH)->willReturn($orderStateMachine);

        $shipment1->getState()->willReturn(ShipmentInterface::STATE_SHIPPED);
        $shipment2->getState()->willReturn(ShipmentInterface::STATE_SHIPPED);

        $orderStateMachine->apply(OrderShippingTransitions::TRANSITION_SHIP)->shouldBeCalled();

        $this->resolve($order);
    }

    function it_marks_an_order_as_shipped_if_there_are_no_shipments_to_deliver(
        FactoryInterface $stateMachineFactory,
        OrderInterface $order,
        StateMachineInterface $orderStateMachine
    ): void {
        $order->getShipments()->willReturn(new ArrayCollection());
        $order->getShippingState()->willReturn(OrderShippingStates::STATE_READY);
        $stateMachineFactory->get($order, OrderShippingTransitions::GRAPH)->willReturn($orderStateMachine);

        $orderStateMachine->apply(OrderShippingTransitions::TRANSITION_SHIP)->shouldBeCalled();

        $this->resolve($order);
    }

    function it_marks_an_order_as_partially_shipped_if_some_shipments_are_delivered(
        FactoryInterface $stateMachineFactory,
        OrderInterface $order,
        ShipmentInterface $shipment1,
        ShipmentInterface $shipment2,
        StateMachineInterface $orderStateMachine
    ): void {
        $shipments = new ArrayCollection();
        $shipments->add($shipment1->getWrappedObject());
        $shipments->add($shipment2->getWrappedObject());

        $order->getShipments()->willReturn($shipments);
        $order->getShippingState()->willReturn(OrderShippingStates::STATE_READY);
        $stateMachineFactory->get($order, OrderShippingTransitions::GRAPH)->willReturn($orderStateMachine);

        $shipment1->getState()->willReturn(ShipmentInterface::STATE_SHIPPED);
        $shipment2->getState()->willReturn(ShipmentInterface::STATE_CANCELLED);

        $orderStateMachine->apply(OrderShippingTransitions::TRANSITION_PARTIALLY_SHIP)->shouldBeCalled();

        $this->resolve($order);
    }

    function it_does_not_mark_an_order_if_it_is_already_in_this_shipping_state(
        FactoryInterface $stateMachineFactory,
        OrderInterface $order,
        ShipmentInterface $shipment1,
        ShipmentInterface $shipment2,
        StateMachineInterface $orderStateMachine
    ): void {
        $shipments = new ArrayCollection();
        $shipments->add($shipment1->getWrappedObject());
        $shipments->add($shipment2->getWrappedObject());

        $order->getShipments()->willReturn($shipments);
        $order->getShippingState()->willReturn(OrderShippingStates::STATE_SHIPPED);
        $stateMachineFactory->get($order, OrderShippingTransitions::GRAPH)->willReturn($orderStateMachine);

        $shipment1->getState()->willReturn(ShipmentInterface::STATE_SHIPPED);
        $shipment2->getState()->willReturn(ShipmentInterface::STATE_SHIPPED);

        $orderStateMachine->apply(OrderShippingTransitions::TRANSITION_SHIP)->shouldNotBeCalled();

        $this->resolve($order);
    }
}
