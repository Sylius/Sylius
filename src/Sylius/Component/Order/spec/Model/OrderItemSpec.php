<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Order\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Order\Model\AdjustableInterface;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItem;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Model\OrderItemUnitInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
final class OrderItemSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(OrderItem::class);
    }

    function it_implements_an_order_item_interface()
    {
        $this->shouldImplement(OrderItemInterface::class);
    }

    function it_implements_an_adjustable_interface()
    {
        $this->shouldImplement(AdjustableInterface::class);
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_does_not_belong_to_an_order_by_default()
    {
        $this->getOrder()->shouldReturn(null);
    }

    function it_allows_assigning_itself_to_an_order(OrderInterface $order)
    {
        $this->setOrder($order);
        $this->getOrder()->shouldReturn($order);
    }

    function it_allows_detaching_itself_from_an_order(OrderInterface $order)
    {
        $this->setOrder($order);
        $this->getOrder()->shouldReturn($order);

        $this->setOrder(null);
        $this->getOrder()->shouldReturn(null);
    }

    function it_does_not_set_order_if_it_is_already_set(OrderInterface $order)
    {
        $this->setOrder($order);
        $this->setOrder($order);

        $order->addItem($this)->shouldBeCalledTimes(1);
    }

    function it_has_quantity_equal_to_0_by_default()
    {
        $this->getQuantity()->shouldReturn(0);
    }

    function it_has_unit_price_equal_to_0_by_default()
    {
        $this->getUnitPrice()->shouldReturn(0);
    }

    function its_unit_price_should_accept_only_integer()
    {
        $this->setUnitPrice(4498);
        $this->getUnitPrice()->shouldReturn(4498);
        $this->getUnitPrice()->shouldBeInteger();
        $this->shouldThrow(\InvalidArgumentException::class)->duringSetUnitPrice(44.98 * 100);
        $this->shouldThrow(\InvalidArgumentException::class)->duringSetUnitPrice('4498');
        $this->shouldThrow(\InvalidArgumentException::class)->duringSetUnitPrice(round(44.98 * 100));
        $this->shouldThrow(\InvalidArgumentException::class)->duringSetUnitPrice([4498]);
        $this->shouldThrow(\InvalidArgumentException::class)->duringSetUnitPrice(new \stdClass());
    }

    function it_has_total_equal_to_0_by_default()
    {
        $this->getTotal()->shouldReturn(0);
    }

    function it_initializes_adjustments_collection_by_default()
    {
        $this->getAdjustments()->shouldHaveType(Collection::class);
    }

    function it_returns_adjustments_recursively(
        AdjustmentInterface $itemAdjustment,
        AdjustmentInterface $unitAdjustment1,
        AdjustmentInterface $unitAdjustment2,
        Collection $unitAdjustments1,
        Collection $unitAdjustments2,
        OrderItemUnitInterface $unit1,
        OrderItemUnitInterface $unit2
    ) {
        $unit1->getOrderItem()->willReturn($this);
        $unit1->getTotal()->willReturn(100);
        $unit1->getAdjustments(null)->willReturn($unitAdjustments1);
        $unitAdjustments1->toArray()->willReturn([$unitAdjustment1]);

        $unit2->getOrderItem()->willReturn($this);
        $unit2->getTotal()->willReturn(100);
        $unit2->getAdjustments(null)->willReturn($unitAdjustments2);
        $unitAdjustments2->toArray()->willReturn([$unitAdjustment2]);

        $this->addUnit($unit1);
        $this->addUnit($unit2);

        $itemAdjustment->setAdjustable($this)->shouldBeCalled();
        $itemAdjustment->isNeutral()->willReturn(true);

        $this->addAdjustment($itemAdjustment);

        $this->getAdjustmentsRecursively()->shouldReturn([$itemAdjustment, $unitAdjustment1, $unitAdjustment2]);
    }

    function it_adds_and_removes_units(OrderItemUnitInterface $orderItemUnit1, OrderItemUnitInterface $orderItemUnit2)
    {
        $orderItemUnit1->getOrderItem()->willReturn($this->getWrappedObject());
        $orderItemUnit1->getTotal()->willReturn(0);
        $orderItemUnit2->getOrderItem()->willReturn($this->getWrappedObject());
        $orderItemUnit2->getTotal()->willReturn(0);
        $this->getUnits()->shouldHaveType(Collection::class);

        $this->addUnit($orderItemUnit1);
        $this->addUnit($orderItemUnit2);
        $this->hasUnit($orderItemUnit1)->shouldReturn(true);
        $this->hasUnit($orderItemUnit2)->shouldReturn(true);

        $this->removeUnit($orderItemUnit1);
        $this->hasUnit($orderItemUnit1)->shouldReturn(false);
        $this->hasUnit($orderItemUnit2)->shouldReturn(true);
    }

    function it_adds_only_unit_that_is_assigned_to_it(OrderItemUnitInterface $orderItemUnit1, OrderItemInterface $orderItem)
    {
        $this
            ->shouldThrow(new \LogicException('This order item unit is assigned to a different order item.'))
            ->duringAddUnit($orderItemUnit1)
        ;

        $orderItemUnit1->getOrderItem()->willReturn($orderItem);
        $this
            ->shouldThrow(new \LogicException('This order item unit is assigned to a different order item.'))
            ->duringAddUnit($orderItemUnit1)
        ;
    }

    function it_recalculates_units_total_on_unit_price_change(
        OrderItemUnitInterface $orderItemUnit1,
        OrderItemUnitInterface $orderItemUnit2
    ) {
        $orderItemUnit1->getOrderItem()->willReturn($this->getWrappedObject());
        $orderItemUnit1->getTotal()->willReturn(0, 100);
        $orderItemUnit2->getOrderItem()->willReturn($this->getWrappedObject());
        $orderItemUnit2->getTotal()->willReturn(0, 100);

        $this->addUnit($orderItemUnit1);
        $this->addUnit($orderItemUnit2);

        $this->setUnitPrice(100);
    }

    function it_adds_adjustments_properly(AdjustmentInterface $adjustment)
    {
        $adjustment->isNeutral()->willReturn(true);
        $adjustment->setAdjustable($this)->shouldBeCalled();

        $this->hasAdjustment($adjustment)->shouldReturn(false);
        $this->addAdjustment($adjustment);
        $this->hasAdjustment($adjustment)->shouldReturn(true);
    }

    function it_removes_adjustments_properly(AdjustmentInterface $adjustment)
    {
        $adjustment->isNeutral()->willReturn(true);
        $adjustment->setAdjustable($this)->shouldBeCalled();

        $this->hasAdjustment($adjustment)->shouldReturn(false);
        $this->addAdjustment($adjustment);
        $this->hasAdjustment($adjustment)->shouldReturn(true);

        $adjustment->setAdjustable(null)->shouldBeCalled();
        $adjustment->isLocked()->willReturn(false);

        $this->removeAdjustment($adjustment);
        $this->hasAdjustment($adjustment)->shouldReturn(false);
    }

    function it_has_correct_total_based_on_unit_items(
        OrderItemUnitInterface $orderItemUnit1,
        OrderItemUnitInterface $orderItemUnit2
    ) {
        $orderItemUnit1->getOrderItem()->willReturn($this->getWrappedObject());
        $orderItemUnit1->getTotal()->willReturn(1499);
        $orderItemUnit2->getOrderItem()->willReturn($this->getWrappedObject());
        $orderItemUnit2->getTotal()->willReturn(1499);

        $this->addUnit($orderItemUnit1);
        $this->addUnit($orderItemUnit2);
        $this->getTotal()->shouldReturn(2998);
    }

    function it_has_correct_total_after_unit_item_remove(
        OrderItemUnitInterface $orderItemUnit1,
        OrderItemUnitInterface $orderItemUnit2
    ) {
        $orderItemUnit1->getOrderItem()->willReturn($this->getWrappedObject());
        $orderItemUnit1->getTotal()->willReturn(2000);
        $orderItemUnit2->getOrderItem()->willReturn($this->getWrappedObject());
        $orderItemUnit2->getTotal()->willReturn(1000);

        $this->addUnit($orderItemUnit1);
        $this->addUnit($orderItemUnit2);
        $this->getTotal()->shouldReturn(3000);

        $this->removeUnit($orderItemUnit2);
        $this->getTotal()->shouldReturn(2000);
    }

    function it_has_correct_total_after_negative_adjustment_add(
        AdjustmentInterface $adjustment,
        OrderItemUnitInterface $orderItemUnit1,
        OrderItemUnitInterface $orderItemUnit2
    ) {
        $orderItemUnit1->getOrderItem()->willReturn($this->getWrappedObject());
        $orderItemUnit1->getTotal()->willReturn(1499);
        $orderItemUnit2->getOrderItem()->willReturn($this->getWrappedObject());
        $orderItemUnit2->getTotal()->willReturn(1499);

        $adjustment->isNeutral()->willReturn(false);
        $adjustment->getAmount()->willReturn(-1000);
        $adjustment->setAdjustable($this)->shouldBeCalled();

        $this->addUnit($orderItemUnit1);
        $this->addUnit($orderItemUnit2);
        $this->addAdjustment($adjustment);
        $this->getTotal()->shouldReturn(1998);
    }

    function it_has_correct_total_after_adjustment_add_and_remove(AdjustmentInterface $adjustment)
    {
        $adjustment->isNeutral()->willReturn(false);
        $adjustment->getAmount()->willReturn(200);
        $adjustment->setAdjustable($this)->shouldBeCalled();

        $this->addAdjustment($adjustment);
        $this->getTotal()->shouldReturn(200);

        $adjustment->setAdjustable(null)->shouldBeCalled();
        $adjustment->isLocked()->willReturn(false);

        $this->removeAdjustment($adjustment);
        $this->getTotal()->shouldReturn(0);
    }

    function it_has_correct_total_after_neutral_adjustment_add_and_remove(AdjustmentInterface $adjustment)
    {
        $adjustment->isNeutral()->willReturn(true);
        $adjustment->getAmount()->willReturn(200);
        $adjustment->setAdjustable($this)->shouldBeCalled();

        $this->addAdjustment($adjustment);
        $this->getTotal()->shouldReturn(0);

        $adjustment->setAdjustable(null)->shouldBeCalled();
        $adjustment->isLocked()->willReturn(false);

        $this->removeAdjustment($adjustment);
        $this->getTotal()->shouldReturn(0);
    }

    function it_has_0_total_when_adjustment_decreases_total_under_0(
        AdjustmentInterface $adjustment,
        OrderItemUnitInterface $orderItemUnit1
    ) {
        $orderItemUnit1->getOrderItem()->willReturn($this->getWrappedObject());
        $orderItemUnit1->getTotal()->willReturn(1499);

        $adjustment->isNeutral()->willReturn(false);
        $adjustment->getAmount()->willReturn(-2000);
        $adjustment->setAdjustable($this)->shouldBeCalled();

        $this->addUnit($orderItemUnit1);
        $this->addAdjustment($adjustment);
        $this->getTotal()->shouldReturn(0);
    }

    function it_has_correct_total_after_unit_price_change(
        OrderItemUnitInterface $orderItemUnit1,
        OrderItemUnitInterface $orderItemUnit2
    ) {
        $orderItemUnit1->getOrderItem()->willReturn($this->getWrappedObject());
        $orderItemUnit1->getTotal()->willReturn(0, 100);
        $orderItemUnit2->getOrderItem()->willReturn($this->getWrappedObject());
        $orderItemUnit2->getTotal()->willReturn(0, 100);

        $this->addUnit($orderItemUnit1);
        $this->addUnit($orderItemUnit2);

        $this->setUnitPrice(100);
        $this->getTotal()->shouldReturn(200);
    }

    function it_has_correct_total_after_order_item_unit_total_change(
        OrderItemUnitInterface $orderItemUnit1,
        OrderItemUnitInterface $orderItemUnit2
    ) {
        $orderItemUnit1->getOrderItem()->willReturn($this->getWrappedObject());
        $orderItemUnit1->getTotal()->willReturn(0);
        $orderItemUnit2->getOrderItem()->willReturn($this->getWrappedObject());
        $orderItemUnit2->getTotal()->willReturn(0, 100);

        $this->addUnit($orderItemUnit1);
        $this->addUnit($orderItemUnit2);

        $this->getTotal()->shouldReturn(0);
        $this->recalculateUnitsTotal();
        $this->getTotal()->shouldReturn(100);
    }

    function it_has_correct_total_after_adjustment_amount_change(
        AdjustmentInterface $adjustment1,
        AdjustmentInterface $adjustment2
    ) {
        $adjustment1->getAmount()->willReturn(100);
        $adjustment1->isNeutral()->willReturn(false);
        $adjustment1->setAdjustable($this)->shouldBeCalled();
        $adjustment2->getAmount()->willReturn(500, 300);
        $adjustment2->isNeutral()->willReturn(false);
        $adjustment2->setAdjustable($this)->shouldBeCalled();

        $this->addAdjustment($adjustment1);
        $this->addAdjustment($adjustment2);

        $this->getTotal()->shouldReturn(600);
        $this->recalculateAdjustmentsTotal();
        $this->getTotal()->shouldReturn(400);
    }

    function it_returns_correct_adjustments_total(
        AdjustmentInterface $adjustment1,
        AdjustmentInterface $adjustment2
    ) {
        $adjustment1->getAmount()->willReturn(100);
        $adjustment1->isNeutral()->willReturn(false);
        $adjustment1->setAdjustable($this)->shouldBeCalled();
        $adjustment2->getAmount()->willReturn(500);
        $adjustment2->isNeutral()->willReturn(false);
        $adjustment2->setAdjustable($this)->shouldBeCalled();

        $this->addAdjustment($adjustment1);
        $this->addAdjustment($adjustment2);

        $this->getAdjustmentsTotal()->shouldReturn(600);
    }

    function it_returns_correct_adjustments_total_by_type(
        AdjustmentInterface $adjustment1,
        AdjustmentInterface $adjustment2,
        AdjustmentInterface $adjustment3
    ) {
        $adjustment1->getType()->willReturn('tax');
        $adjustment1->getAmount()->willReturn(200);
        $adjustment1->isNeutral()->willReturn(false);
        $adjustment1->setAdjustable($this)->shouldBeCalled();
        $adjustment2->getType()->willReturn('tax');
        $adjustment2->getAmount()->willReturn(-50);
        $adjustment2->isNeutral()->willReturn(false);
        $adjustment2->setAdjustable($this)->shouldBeCalled();

        $adjustment3->getType()->willReturn('promotion');
        $adjustment3->getAmount()->willReturn(-1000);
        $adjustment3->isNeutral()->willReturn(false);
        $adjustment3->setAdjustable($this)->shouldBeCalled();

        $this->addAdjustment($adjustment1);
        $this->addAdjustment($adjustment2);
        $this->addAdjustment($adjustment3);

        $this->getAdjustmentsTotal('tax')->shouldReturn(150);
        $this->getAdjustmentsTotal('promotion')->shouldReturn(-1000);
        $this->getAdjustmentsTotal('any')->shouldReturn(0);
    }

    function it_returns_correct_adjustments_total_recursively(
        AdjustmentInterface $adjustment1,
        AdjustmentInterface $taxAdjustment1,
        AdjustmentInterface $taxAdjustment2,
        OrderItemUnitInterface $orderItemUnit1,
        OrderItemUnitInterface $orderItemUnit2
    ) {
        $adjustment1->getAmount()->willReturn(200);
        $adjustment1->isNeutral()->willReturn(false);
        $adjustment1->setAdjustable($this)->shouldBeCalled();

        $taxAdjustment1->getAmount()->willReturn(150);
        $taxAdjustment1->isNeutral()->willReturn(false);
        $taxAdjustment2->getAmount()->willReturn(100);
        $taxAdjustment2->isNeutral()->willReturn(false);

        $orderItemUnit1->getOrderItem()->willReturn($this->getWrappedObject());
        $orderItemUnit1->getTotal()->willReturn(500);
        $orderItemUnit1->getAdjustments(null)->willReturn(new ArrayCollection([$taxAdjustment1->getWrappedObject()]));
        $orderItemUnit2->getOrderItem()->willReturn($this->getWrappedObject());
        $orderItemUnit2->getTotal()->willReturn(300);
        $orderItemUnit2->getAdjustments(null)->willReturn(new ArrayCollection([$taxAdjustment2->getWrappedObject()]));

        $this->addAdjustment($adjustment1);
        $this->addUnit($orderItemUnit1);
        $this->addUnit($orderItemUnit2);

        $this->getAdjustmentsTotalRecursively()->shouldReturn(450);
    }

    function it_returns_correct_adjustments_total_by_type_recursively(
        AdjustmentInterface $adjustment1,
        AdjustmentInterface $promotionAdjustment,
        AdjustmentInterface $taxAdjustment1,
        AdjustmentInterface $taxAdjustment2,
        OrderItemUnitInterface $orderItemUnit1,
        OrderItemUnitInterface $orderItemUnit2
    ) {
        $adjustment1->getType()->willReturn('tax');
        $adjustment1->getAmount()->willReturn(200);
        $adjustment1->isNeutral()->willReturn(false);
        $adjustment1->setAdjustable($this)->shouldBeCalled();

        $promotionAdjustment->getAmount()->willReturn(30);
        $promotionAdjustment->isNeutral()->willReturn(false);
        $taxAdjustment1->getAmount()->willReturn(150);
        $taxAdjustment1->isNeutral()->willReturn(false);
        $taxAdjustment2->getAmount()->willReturn(100);
        $taxAdjustment2->isNeutral()->willReturn(false);

        $orderItemUnit1->getOrderItem()->willReturn($this->getWrappedObject());
        $orderItemUnit1->getTotal()->willReturn(500);
        $orderItemUnit1->getAdjustments('tax')->willReturn(new ArrayCollection([$taxAdjustment1->getWrappedObject()]));
        $orderItemUnit1->getAdjustments('promotion')->willReturn(new ArrayCollection([$promotionAdjustment->getWrappedObject()]));
        $orderItemUnit2->getOrderItem()->willReturn($this->getWrappedObject());
        $orderItemUnit2->getTotal()->willReturn(300);
        $orderItemUnit2->getAdjustments('tax')->willReturn(new ArrayCollection([$taxAdjustment2->getWrappedObject()]));
        $orderItemUnit2->getAdjustments('promotion')->willReturn(new ArrayCollection());

        $this->addAdjustment($adjustment1);
        $this->addUnit($orderItemUnit1);
        $this->addUnit($orderItemUnit2);

        $this->getAdjustmentsTotalRecursively('tax')->shouldReturn(450);
        $this->getAdjustmentsTotalRecursively('promotion')->shouldReturn(30);
    }

    function it_can_be_immutable()
    {
        $this->setImmutable(true);
        $this->isImmutable()->shouldReturn(true);
    }
}
