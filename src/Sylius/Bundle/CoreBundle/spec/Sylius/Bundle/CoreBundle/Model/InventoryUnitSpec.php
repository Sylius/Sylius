<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Shipping\Model\ShipmentItemInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface;

class InventoryUnitSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Model\InventoryUnit');
    }

    function it_implements_Sylius_core_inventory_unit_interface()
    {
        $this->shouldImplement('Sylius\Component\Core\Model\InventoryUnitInterface');
    }

    function it_extends_Sylius_inventory_unit_model()
    {
        $this->shouldHaveType('Sylius\Component\Inventory\Model\InventoryUnit');
    }

    function it_implements_Sylius_shipment_item_interface()
    {
        $this->shouldImplement('Sylius\Component\Shipping\Model\ShipmentItemInterface');
    }

    function it_does_not_belong_to_any_shipment_by_default()
    {
        $this->getShipment()->shouldReturn(null);
    }

    function it_allows_assigning_itself_to_a_shipment(ShipmentInterface $shipment)
    {
        $this->setShipment($shipment);
        $this->getShipment()->shouldReturn($shipment);
    }

    function it_allows_detaching_itself_from_a_shipment(ShipmentInterface $shipment)
    {
        $this->setShipment($shipment);
        $this->getShipment()->shouldReturn($shipment);

        $this->setShipment(null);
        $this->getShipment()->shouldReturn(null);
    }

    function it_has_checkout_shipping_state_by_default()
    {
        $this->getShippingState()->shouldReturn(ShipmentInterface::STATE_CHECKOUT);
    }

    function its_shipping_state_is_mutable()
    {
        $this->setShippingState(ShipmentInterface::STATE_SHIPPED);
        $this->getShippingState()->shouldReturn(ShipmentInterface::STATE_SHIPPED);
    }

    function it_does_not_belong_to_an_order_item_by_default()
    {
        $this->getOrderItem()->shouldReturn(null);
    }

    function it_allows_attaching_itself_to_an_order_item(OrderItemInterface $order_item)
    {
        $this->setOrderItem($order_item);
        $this->getOrderItem()->shouldReturn($order_item);
    }

    function it_allows_detaching_itself_from_an_order_item(OrderItemInterface $order_item)
    {
        $this->setOrderItem($order_item);
        $this->getOrderItem()->shouldReturn($order_item);

        $this->setOrderItem(null);
        $this->getOrderItem()->shouldReturn(null);
    }
}
