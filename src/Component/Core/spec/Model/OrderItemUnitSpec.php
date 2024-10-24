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

namespace spec\Sylius\Component\Core\Model;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Order\Model\OrderItemUnit as BaseOrderItemUnit;
use Sylius\Component\Shipping\Model\ShipmentUnitInterface;

final class OrderItemUnitSpec extends ObjectBehavior
{
    function let(OrderItemInterface $orderItem): void
    {
        $orderItem->getUnitPrice()->willReturn(1000);
        $orderItem->addUnit(Argument::type(OrderItemUnitInterface::class))->shouldBeCalled();
        $this->beConstructedWith($orderItem);
    }

    function it_implements_an_order_item_unit_interface(): void
    {
        $this->shouldImplement(OrderItemUnitInterface::class);
    }

    function it_implements_an_inventory_unit_interface(): void
    {
        $this->shouldImplement(InventoryUnitInterface::class);
    }

    function it_implements_a_shipment_unit_interface(): void
    {
        $this->shouldImplement(ShipmentUnitInterface::class);
    }

    function it_is_an_order_item_unit(): void
    {
        $this->shouldHaveType(BaseOrderItemUnit::class);
    }

    function its_shipment_is_mutable(ShipmentInterface $shipment): void
    {
        $this->setShipment($shipment);
        $this->getShipment()->shouldReturn($shipment);
    }

    function its_created_at_is_mutable(\DateTime $createdAt): void
    {
        $this->setCreatedAt($createdAt);
        $this->getCreatedAt()->shouldReturn($createdAt);
    }

    function its_updated_at_is_mutable(\DateTime $updatedAt): void
    {
        $this->setUpdatedAt($updatedAt);
        $this->getUpdatedAt()->shouldReturn($updatedAt);
    }

    function its_stockable_is_an_order_item_variant(OrderItemInterface $orderItem, ProductVariantInterface $variant): void
    {
        $orderItem->getVariant()->willReturn($variant);

        $this->getStockable()->shouldReturn($variant);
    }

    function its_shippable_is_an_order_item_variant(OrderItemInterface $orderItem, ProductVariantInterface $variant): void
    {
        $orderItem->getVariant()->willReturn($variant);

        $this->getShippable()->shouldReturn($variant);
    }

    function it_returns_0_tax_total_when_there_are_no_tax_adjustments(): void
    {
        $this->getTaxTotal()->shouldReturn(0);
    }

    function it_returns_a_sum_of_neutral_and_non_neutral_tax_adjustments_as_tax_total(
        OrderItemInterface $orderItem,
        AdjustmentInterface $nonNeutralTaxAdjustment,
        AdjustmentInterface $neutralTaxAdjustment,
    ): void {
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
        AdjustmentInterface $notTaxAdjustment,
    ): void {
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
