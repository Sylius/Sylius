<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ShippingBundle\Model\ShipmentItemInterface;

class InventoryUnitSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Model\InventoryUnit');
    }

    function it_implements_Sylius_core_inventory_unit_interface()
    {
        $this->shouldImplement('Sylius\Bundle\CoreBundle\Model\InventoryUnitInterface');
    }

    function it_extends_Sylius_inventory_unit_model()
    {
        $this->shouldHaveType('Sylius\Bundle\InventoryBundle\Model\InventoryUnit');
    }

    function it_implements_Sylius_shipment_item_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ShippingBundle\Model\ShipmentItemInterface');
    }

    function it_does_not_belong_to_any_shipment_by_default()
    {
        $this->getShipment()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentInterface $shipment
     */
    function it_allows_assigning_itself_to_a_shipment($shipment)
    {
        $this->setShipment($shipment);
        $this->getShipment()->shouldReturn($shipment);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentInterface $shipment
     */
    function it_allows_detaching_itself_from_a_shipment($shipment)
    {
        $this->setShipment($shipment);
        $this->getShipment()->shouldReturn($shipment);

        $this->setShipment(null);
        $this->getShipment()->shouldReturn(null);
    }

    function it_has_ready_shipping_state_by_default()
    {
        $this->getShippingState()->shouldReturn(ShipmentItemInterface::STATE_READY);
    }

    function its_shipping_state_is_mutable()
    {
        $this->setShippingState(ShipmentItemInterface::STATE_SHIPPED);
        $this->getShippingState()->shouldReturn(ShipmentItemInterface::STATE_SHIPPED);
    }

    function it_does_not_belong_to_an_order_by_default()
    {
        $this->getOrder()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\CoreBundle\Model\OrderInterface $order
     */
    function it_allows_attaching_itself_to_an_order($order)
    {
        $this->setOrder($order);
        $this->getOrder()->shouldReturn($order);
    }

    /**
     * @param Sylius\Bundle\CoreBundle\Model\OrderInterface $order
     */
    function it_allows_detaching_itself_from_an_order($order)
    {
        $this->setOrder($order);
        $this->getOrder()->shouldReturn($order);

        $this->setOrder(null);
        $this->getOrder()->shouldReturn(null);
    }
}
