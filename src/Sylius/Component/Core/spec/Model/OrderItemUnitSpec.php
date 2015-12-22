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
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Order\Model\OrderItemUnit;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Sylius\Component\Shipping\Model\ShippableInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class OrderItemUnitSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Model\OrderItemUnit');
    }

    function it_implements_order_item_unit_interface()
    {
        $this->shouldImplement(OrderItemUnitInterface::class);
    }

    function it_is_order_item_unit()
    {
        $this->shouldHaveType(OrderItemUnit::class);
    }

    function its_inventory_state_is_mutable()
    {
        $this->setInventoryState('state');
        $this->getInventoryState()->shouldReturn('state');
    }

    function its_shipment_is_mutable(ShipmentInterface $shipment)
    {
        $this->setShipment($shipment);
        $this->getShipment()->shouldReturn($shipment);
    }

    function its_order_item_is_mutable(OrderItemInterface $orderItem)
    {
        $this->setOrderItem($orderItem);
        $this->getOrderItem()->shouldReturn($orderItem);
    }

    function its_created_at_is_mutable(\DateTime $createdAt)
    {
        $this->setCreatedAt($createdAt);
        $this->getCreatedAt()->shouldReturn($createdAt);
    }

    function its_updated_at_is_mutable(\DateTime $updatedAt)
    {
        $this->setUpdatedAt($updatedAt);
        $this->getUpdatedAt()->shouldReturn($updatedAt);
    }
}
