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
use Prophecy\Argument;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnit;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Order\Model\OrderItemUnit as BaseOrderItemUnit;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Sylius\Component\Shipping\Model\ShipmentUnitInterface;

/**
 * @mixin OrderItemUnit
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class OrderItemUnitSpec extends ObjectBehavior
{
    function let(OrderItemInterface $orderItem)
    {
        $orderItem->getUnitPrice()->willReturn(1000);
        $orderItem->addUnit(Argument::type(OrderItemUnitInterface::class))->shouldBeCalled();
        $this->beConstructedWith($orderItem);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OrderItemUnit::class);
    }

    function it_implements_order_item_unit_interface()
    {
        $this->shouldImplement(OrderItemUnitInterface::class);
    }

    function it_implements_inventory_unit_interface()
    {
        $this->shouldImplement(InventoryUnitInterface::class);
    }

    function it_implements_shipment_unit_interface()
    {
        $this->shouldImplement(ShipmentUnitInterface::class);
    }

    function it_is_an_order_item_unit()
    {
        $this->shouldHaveType(BaseOrderItemUnit::class);
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

        $this->getStockable()->shouldReturn($variant);
    }

    function it_can_be_sold()
    {
        $this->setInventoryState(InventoryUnitInterface::STATE_SOLD);
        $this->shouldBeSold();
    }

    function its_shippable_is_order_item_variant(OrderItemInterface $orderItem, ProductVariantInterface $variant)
    {
        $orderItem->getVariant()->willReturn($variant);

        $this->getShippable()->shouldReturn($variant);
    }

    function its_shipping_state_is_mutable()
    {
        $this->setShippingState(ShipmentInterface::STATE_SHIPPED);
        $this->getShippingState()->shouldReturn(ShipmentInterface::STATE_SHIPPED);
    }

    function it_returns_0_tax_total_when_there_are_no_tax_adjustments()
    {
        $this->getTaxTotal()->shouldReturn(0);
    }

    function it_returns_sum_of_neutral_and_non_neutral_tax_adjustments_as_tax_total(
        OrderItemInterface $orderItem,
        AdjustmentInterface $nonNeutralTaxAdjustment,
        AdjustmentInterface $neutralTaxAdjustment
    ) {
        $neutralTaxAdjustment->isNeutral()->willReturn(true);
        $neutralTaxAdjustment->getType()->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $neutralTaxAdjustment->getAmount()->willReturn(200);
        $nonNeutralTaxAdjustment->isNeutral()->willReturn(false);
        $nonNeutralTaxAdjustment->getType()->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $nonNeutralTaxAdjustment->getAmount()->willReturn(300);

        $orderItem->recalculateUnitsTotal()->shouldBeCalled();
        $neutralTaxAdjustment->setAdjustable($this)->shouldBeCalled();
        $nonNeutralTaxAdjustment->setAdjustable($this)->shouldBeCalled();
        $this->addAdjustment($neutralTaxAdjustment);
        $this->addAdjustment($nonNeutralTaxAdjustment);

        $this->getTaxTotal()->shouldReturn(500);
    }

    function it_returns_only_sum_of_neutral_and_non_neutral_tax_adjustments_as_tax_total(
        OrderItemInterface $orderItem,
        AdjustmentInterface $nonNeutralTaxAdjustment,
        AdjustmentInterface $neutralTaxAdjustment,
        AdjustmentInterface $notTaxAdjustment
    ) {
        $neutralTaxAdjustment->isNeutral()->willReturn(true);
        $neutralTaxAdjustment->getType()->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $neutralTaxAdjustment->getAmount()->willReturn(200);
        $nonNeutralTaxAdjustment->isNeutral()->willReturn(false);
        $nonNeutralTaxAdjustment->getType()->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $nonNeutralTaxAdjustment->getAmount()->willReturn(300);
        $notTaxAdjustment->isNeutral()->willReturn(false);
        $notTaxAdjustment->getType()->willReturn(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT);
        $notTaxAdjustment->getAmount()->willReturn(100);

        $orderItem->recalculateUnitsTotal()->shouldBeCalled();
        $neutralTaxAdjustment->setAdjustable($this)->shouldBeCalled();
        $nonNeutralTaxAdjustment->setAdjustable($this)->shouldBeCalled();
        $notTaxAdjustment->setAdjustable($this)->shouldBeCalled();
        $this->addAdjustment($neutralTaxAdjustment);
        $this->addAdjustment($nonNeutralTaxAdjustment);
        $this->addAdjustment($notTaxAdjustment);

        $this->getTaxTotal()->shouldReturn(500);
    }
}
