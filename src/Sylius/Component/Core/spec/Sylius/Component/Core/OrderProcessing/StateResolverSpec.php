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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderShippingStates;
use Sylius\Component\Core\Model\ShipmentInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
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

    function it_marks_order_as_shipped_if_all_shipments_devliered(
        OrderInterface $order,
        ShipmentInterface $shipment1,
        ShipmentInterface $shipment2
    )
    {
        $order->isBackorder()->shouldBeCalled()->willReturn(false);
        $order->getShipments()->willReturn(array($shipment1, $shipment2));

        $shipment1->getState()->willReturn(ShipmentInterface::STATE_SHIPPED);
        $shipment2->getState()->willReturn(ShipmentInterface::STATE_SHIPPED);

        $order->setShippingState(OrderShippingStates::SHIPPED)->shouldBeCalled();
        $this->resolveShippingState($order);
    }

    function it_marks_order_as_partially_shipped_if_not_all_shipments_devliered(
        OrderInterface $order,
        ShipmentInterface $shipment1,
        ShipmentInterface $shipment2
    )
    {
        $order->isBackorder()->shouldBeCalled()->willReturn(false);
        $order->getShipments()->willReturn(array($shipment1, $shipment2));

        $shipment1->getState()->willReturn(ShipmentInterface::STATE_SHIPPED);
        $shipment2->getState()->willReturn(ShipmentInterface::STATE_READY);

        $order->setShippingState(OrderShippingStates::PARTIALLY_SHIPPED)->shouldBeCalled();
        $this->resolveShippingState($order);
    }

    function it_marks_order_as_returned_if_all_shipments_were_returned(
        OrderInterface $order,
        ShipmentInterface $shipment1,
        ShipmentInterface $shipment2
    )
    {
        $order->isBackorder()->shouldBeCalled()->willReturn(false);
        $order->getShipments()->willReturn(array($shipment1, $shipment2));

        $shipment1->getState()->willReturn(ShipmentInterface::STATE_RETURNED);
        $shipment2->getState()->willReturn(ShipmentInterface::STATE_RETURNED);

        $order->setShippingState(OrderShippingStates::RETURNED)->shouldBeCalled();
        $this->resolveShippingState($order);
    }
}
