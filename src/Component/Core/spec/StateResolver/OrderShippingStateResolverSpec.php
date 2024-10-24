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

namespace spec\Sylius\Component\Core\StateResolver;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\OrderShippingStates;
use Sylius\Component\Core\OrderShippingTransitions;
use Sylius\Component\Order\StateResolver\StateResolverInterface;

final class OrderShippingStateResolverSpec extends ObjectBehavior
{
    function let(StateMachineInterface $stateMachine): void
    {
        $this->beConstructedWith($stateMachine);
    }

    function it_implements_an_order_state_resolver_interface(): void
    {
        $this->shouldImplement(StateResolverInterface::class);
    }

    function it_marks_an_order_as_shipped_if_all_shipments_delivered(
        StateMachineInterface $stateMachine,
        OrderInterface $order,
        ShipmentInterface $shipment1,
        ShipmentInterface $shipment2,
    ): void {
        $shipments = new ArrayCollection();
        $shipments->add($shipment1->getWrappedObject());
        $shipments->add($shipment2->getWrappedObject());

        $order->getShipments()->willReturn($shipments);
        $order->getShippingState()->willReturn(OrderShippingStates::STATE_READY);

        $shipment1->getState()->willReturn(ShipmentInterface::STATE_SHIPPED);
        $shipment2->getState()->willReturn(ShipmentInterface::STATE_SHIPPED);

        $stateMachine
            ->apply($order, OrderShippingTransitions::GRAPH, OrderShippingTransitions::TRANSITION_SHIP)
            ->shouldBeCalled()
        ;

        $this->resolve($order);
    }

    function it_marks_an_order_as_shipped_if_there_are_no_shipments_to_deliver(
        StateMachineInterface $stateMachine,
        OrderInterface $order,
    ): void {
        $order->getShipments()->willReturn(new ArrayCollection());
        $order->getShippingState()->willReturn(OrderShippingStates::STATE_READY);

        $stateMachine
            ->apply($order, OrderShippingTransitions::GRAPH, OrderShippingTransitions::TRANSITION_SHIP)
            ->shouldBeCalled()
        ;

        $this->resolve($order);
    }

    function it_marks_an_order_as_partially_shipped_if_some_shipments_are_delivered(
        StateMachineInterface $stateMachine,
        OrderInterface $order,
        ShipmentInterface $shipment1,
        ShipmentInterface $shipment2,
    ): void {
        $shipments = new ArrayCollection();
        $shipments->add($shipment1->getWrappedObject());
        $shipments->add($shipment2->getWrappedObject());

        $order->getShipments()->willReturn($shipments);
        $order->getShippingState()->willReturn(OrderShippingStates::STATE_READY);

        $shipment1->getState()->willReturn(ShipmentInterface::STATE_SHIPPED);
        $shipment2->getState()->willReturn(ShipmentInterface::STATE_CANCELLED);

        $stateMachine
            ->apply($order, OrderShippingTransitions::GRAPH, OrderShippingTransitions::TRANSITION_PARTIALLY_SHIP)
            ->shouldBeCalled()
        ;

        $this->resolve($order);
    }

    function it_does_not_mark_an_order_if_it_is_already_in_this_shipping_state(
        StateMachineInterface $stateMachine,
        OrderInterface $order,
        ShipmentInterface $shipment1,
        ShipmentInterface $shipment2,
    ): void {
        $shipments = new ArrayCollection();
        $shipments->add($shipment1->getWrappedObject());
        $shipments->add($shipment2->getWrappedObject());

        $order->getShipments()->willReturn($shipments);
        $order->getShippingState()->willReturn(OrderShippingStates::STATE_SHIPPED);

        $shipment1->getState()->willReturn(ShipmentInterface::STATE_SHIPPED);
        $shipment2->getState()->willReturn(ShipmentInterface::STATE_SHIPPED);

        $stateMachine->apply(Argument::cetera())->shouldNotBeCalled();

        $this->resolve($order);
    }
}
