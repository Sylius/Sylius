<?php

namespace spec\Sylius\Component\Core\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItem;
use Sylius\Component\Core\Model\OrderItemUnitInterface;

/**
 * @mixin OrderItem
 */
final class OrderItemSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(OrderItem::class);
    }

    function it_returns_0_tax_total_when_there_are_no_units()
    {
        $this->getTaxTotal()->shouldReturn(0);
    }

    function it_returns_tax_of_all_unit(OrderItemUnitInterface $orderItemUnit1, OrderItemUnitInterface $orderItemUnit2)
    {
        $orderItemUnit1->getTotal()->willReturn(1200);
        $orderItemUnit1->getTaxTotal()->willReturn(200);
        $orderItemUnit1->getOrderItem()->willReturn($this);
        $orderItemUnit2->getTotal()->willReturn(1120);
        $orderItemUnit2->getTaxTotal()->willReturn(120);
        $orderItemUnit2->getOrderItem()->willReturn($this);

        $this->addUnit($orderItemUnit1);
        $this->addUnit($orderItemUnit2);

        $this->getTaxTotal()->shouldReturn(320);
    }

    function it_returns_tax_of_all_units_and_both_neutral_and_non_neutral_tax_adjustments(
        OrderItemUnitInterface $orderItemUnit1,
        OrderItemUnitInterface $orderItemUnit2,
        AdjustmentInterface $nonNeutralTaxAdjustment,
        AdjustmentInterface $neutralTaxAdjustment
    ) {
        $orderItemUnit1->getTotal()->willReturn(1200);
        $orderItemUnit1->getTaxTotal()->willReturn(200);
        $orderItemUnit1->getOrderItem()->willReturn($this);
        $orderItemUnit2->getTotal()->willReturn(1120);
        $orderItemUnit2->getTaxTotal()->willReturn(120);
        $orderItemUnit2->getOrderItem()->willReturn($this);

        $this->addUnit($orderItemUnit1);
        $this->addUnit($orderItemUnit2);

        $neutralTaxAdjustment->isNeutral()->willReturn(true);
        $neutralTaxAdjustment->getType()->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $neutralTaxAdjustment->getAmount()->willReturn(200);
        $nonNeutralTaxAdjustment->isNeutral()->willReturn(false);
        $nonNeutralTaxAdjustment->getType()->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $nonNeutralTaxAdjustment->getAmount()->willReturn(300);

        $neutralTaxAdjustment->setAdjustable($this)->shouldBeCalled();
        $nonNeutralTaxAdjustment->setAdjustable($this)->shouldBeCalled();
        $this->addAdjustment($neutralTaxAdjustment);
        $this->addAdjustment($nonNeutralTaxAdjustment);

        $this->getTaxTotal()->shouldReturn(820);
    }

    function it_returns_discounted_unit_price_which_is_first_unit_price_lowered_by_unit_promotions(
        OrderItemUnitInterface $unit
    ) {
        $this->setUnitPrice(10000);

        $unit->getOrderItem()->willReturn($this);
        $unit->getTotal()->willReturn(9000);
        $unit->getAdjustmentsTotal(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)->willReturn(-500);

        $this->addUnit($unit);

        $this->getDiscountedUnitPrice()->shouldReturn(9500);
    }

    function it_returns_unit_price_as_discounted_unit_price_if_there_are_no_units()
    {
        $this->setUnitPrice(10000);

        $this->getDiscountedUnitPrice()->shouldReturn(10000);
    }

    function it_returns_subtotal_which_consist_of_discounted_unit_price_multiplied_by_quantity(
        OrderItemUnitInterface $firstUnit,
        OrderItemUnitInterface $secondUnit
    ) {
        $this->setUnitPrice(10000);

        $firstUnit->getOrderItem()->willReturn($this);
        $firstUnit->getTotal()->willReturn(9000);
        $firstUnit->getAdjustmentsTotal(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)->willReturn(-500);

        $secondUnit->getOrderItem()->willReturn($this);
        $secondUnit->getTotal()->willReturn(9000);
        $secondUnit->getAdjustmentsTotal(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)->willReturn(-500);

        $this->addUnit($firstUnit);
        $this->addUnit($secondUnit);

        $this->getSubtotal()->shouldReturn(19000);
    }
}
