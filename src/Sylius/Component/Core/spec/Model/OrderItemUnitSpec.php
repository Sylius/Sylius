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
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
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

    function it_stockable_is_order_item_variant(OrderItemInterface $orderItem, ProductVariantInterface $variant)
    {
        $orderItem->getVariant()->willReturn($variant);
        $this->setOrderItem($orderItem);

        $this->getStockable()->shouldReturn($variant);
    }

    function its_sku_is_order_item_variant_sku(OrderItemInterface $orderItem, ProductVariantInterface $variant)
    {
        $variant->getSku()->willReturn('test_sku');
        $orderItem->getVariant()->willReturn($variant);
        $this->setOrderItem($orderItem);

        $this->getSku()->shouldReturn('test_sku');
    }

    function it_can_be_sold_or_backorderded()
    {
        $this->setInventoryState(InventoryUnitInterface::STATE_SOLD);
        $this->shouldBeSold();

        $this->setInventoryState(InventoryUnitInterface::STATE_BACKORDERED);
        $this->shouldBeBackordered();
        $this->shouldNotBeSold();
    }

    function its_shippable_is_order_item_variant(OrderItemInterface $orderItem, ProductVariantInterface $variant)
    {
        $orderItem->getVariant()->willReturn($variant);
        $this->setOrderItem($orderItem);

        $this->getShippable()->shouldReturn($variant);
    }

    function its_shipping_state_is_mutable()
    {
        $this->setShippingState(ShipmentInterface::STATE_SHIPPED);
        $this->getShippingState()->shouldReturn(ShipmentInterface::STATE_SHIPPED);
    }

}
