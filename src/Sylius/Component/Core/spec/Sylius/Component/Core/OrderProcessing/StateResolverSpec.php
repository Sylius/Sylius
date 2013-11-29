<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\OrderProcessing;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\InventoryUnitInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderShippingStates;
use Sylius\Component\Core\Model\ShipmentInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class StateResolverSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\OrderProcessing\StateResolver');
    }

    function it_implements_Sylius_order_state_resolver_interface()
    {
        $this->shouldImplement('Sylius\Component\Core\OrderProcessing\StateResolverInterface');
    }

    function it_marks_order_as_a_backorders_if_it_contains_backordered_units(OrderInterface $order)
    {
        $order->isBackorder()->shouldBeCalled()->willReturn(true);

        $order->setShippingState(OrderShippingStates::BACKORDER)->shouldBeCalled();
        $this->resolveShippingState($order);
    }

    function it_marks_order_as_shipped_if_all_shipments_delivered(OrderInterface $order, ShipmentInterface $shipment1, ShipmentInterface $shipment2)
    {
        $order->isBackorder()->shouldBeCalled()->willReturn(false);
        $order->getShipments()->willReturn(array($shipment1, $shipment2));

        $shipment1->getState()->willReturn(ShipmentInterface::STATE_SHIPPED);
        $shipment2->getState()->willReturn(ShipmentInterface::STATE_SHIPPED);

        $order->setShippingState(OrderShippingStates::SHIPPED)->shouldBeCalled();
        $this->resolveShippingState($order);
    }

    function it_marks_order_as_partially_shipped_if_not_all_shipments_delivered(OrderInterface $order, ShipmentInterface $shipment1, ShipmentInterface $shipment2)
    {
        $order->isBackorder()->shouldBeCalled()->willReturn(false);
        $order->getShipments()->willReturn(array($shipment1, $shipment2));

        $shipment1->getState()->willReturn(ShipmentInterface::STATE_SHIPPED);
        $shipment2->getState()->willReturn(ShipmentInterface::STATE_DISPATCHED);

        $order->setShippingState(OrderShippingStates::PARTIALLY_SHIPPED)->shouldBeCalled();
        $this->resolveShippingState($order);
    }

    function it_marks_order_as_dispatched_if_all_shipments_are_dispatched(OrderInterface $order, ShipmentInterface $shipment1, ShipmentInterface $shipment2)
    {
        $order->isBackorder()->shouldBeCalled()->willReturn(false);
        $order->getShipments()->willReturn(array($shipment1, $shipment2));

        $shipment1->getState()->willReturn(ShipmentInterface::STATE_DISPATCHED);
        $shipment2->getState()->willReturn(ShipmentInterface::STATE_DISPATCHED);

        $order->setShippingState(OrderShippingStates::DISPATCHED)->shouldBeCalled();
        $this->resolveShippingState($order);
    }

    function it_marks_order_as_returned_if_all_shipments_were_returned(OrderInterface $order, ShipmentInterface $shipment1, ShipmentInterface $shipment2)
    {
        $order->isBackorder()->shouldBeCalled()->willReturn(false);
        $order->getShipments()->willReturn(array($shipment1, $shipment2));

        $shipment1->getState()->willReturn(ShipmentInterface::STATE_RETURNED);
        $shipment2->getState()->willReturn(ShipmentInterface::STATE_RETURNED);

        $order->setShippingState(OrderShippingStates::RETURNED)->shouldBeCalled();
        $this->resolveShippingState($order);
    }
}
