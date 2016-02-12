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

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Model\OrderItemUnitInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class OrderItemUnitSpec extends ObjectBehavior
{
    function let(OrderItemInterface $orderItem)
    {
        $orderItem->getUnitPrice()->willReturn(1000);
        $orderItem->addUnit(Argument::type(OrderItemUnitInterface::class))->shouldBeCalled();
        $this->beConstructedWith($orderItem);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Order\Model\OrderItemUnit');
    }

    function it_implements_order_item_unit_interface()
    {
        $this->shouldImplement(OrderItemUnitInterface::class);
    }

    function it_has_correct_total_when_there_are_no_adjustments()
    {
        $this->getTotal()->shouldReturn(1000);
    }

    function it_adds_and_removes_adjustments(AdjustmentInterface $adjustment, OrderItemInterface $orderItem)
    {
        $orderItem->recalculateUnitsTotal()->shouldBeCalled();

        $adjustment->isNeutral()->willReturn(true);
        $adjustment->setAdjustable($this)->shouldBeCalled();

        $this->addAdjustment($adjustment);
        $this->hasAdjustment($adjustment)->shouldReturn(true);

        $adjustment->setAdjustable(null)->shouldBeCalled();
        $adjustment->isLocked()->willReturn(false);

        $this->removeAdjustment($adjustment);
        $this->hasAdjustment($adjustment)->shouldReturn(false);
    }

    function it_does_not_remove_adjustment_when_it_is_locked(
        AdjustmentInterface $adjustment,
        OrderItemInterface $orderItem
    ) {
        $orderItem->recalculateUnitsTotal()->shouldBeCalledTimes(1);

        $adjustment->isNeutral()->willReturn(true);
        $adjustment->setAdjustable($this)->shouldBeCalled();

        $this->addAdjustment($adjustment);

        $adjustment->setAdjustable(null)->shouldNotBeCalled();
        $adjustment->isLocked()->willReturn(true);

        $this->removeAdjustment($adjustment);
        $this->hasAdjustment($adjustment)->shouldReturn(true);
    }

    function it_has_correct_total_after_adjustment_add_and_remove(
        AdjustmentInterface $adjustment1,
        AdjustmentInterface $adjustment2,
        AdjustmentInterface $adjustment3,
        OrderItemInterface $orderItem
    ) {
        $orderItem->recalculateUnitsTotal()->shouldBeCalledTimes(4);

        $adjustment1->isNeutral()->willReturn(false);
        $adjustment1->isRefund()->willReturn(false);
        $adjustment1->setAdjustable($this)->shouldBeCalled();
        $adjustment1->isLocked()->willReturn(false);
        $adjustment1->setAdjustable(null)->shouldBeCalled();
        $adjustment1->getAmount()->willReturn(100);

        $adjustment2->isNeutral()->willReturn(false);
        $adjustment2->isRefund()->willReturn(false);
        $adjustment2->setAdjustable($this)->shouldBeCalled();
        $adjustment2->getAmount()->willReturn(50);

        $adjustment3->isNeutral()->willReturn(false);
        $adjustment3->isRefund()->willReturn(false);
        $adjustment3->setAdjustable($this)->shouldBeCalled();
        $adjustment3->getAmount()->willReturn(250);

        $this->addAdjustment($adjustment1);
        $this->addAdjustment($adjustment2);

        $this->getTotal()->shouldReturn(1150);

        $this->addAdjustment($adjustment3);
        $this->removeAdjustment($adjustment1);

        $this->getTotal()->shouldReturn(1300);
    }

    function it_has_correct_total_after_adjustments_clear(
        AdjustmentInterface $adjustment1,
        AdjustmentInterface $adjustment2,
        OrderItemInterface $orderItem
    ) {
        $orderItem->recalculateUnitsTotal()->shouldBeCalled(3);

        $adjustment1->isNeutral()->willReturn(false);
        $adjustment1->isRefund()->willReturn(false);
        $adjustment1->getAmount()->willReturn(200);
        $adjustment1->setAdjustable($this)->shouldBeCalled();

        $adjustment2->isNeutral()->willReturn(false);
        $adjustment2->isRefund()->willReturn(false);
        $adjustment2->getAmount()->willReturn(300);
        $adjustment2->setAdjustable($this)->shouldBeCalled();

        $this->addAdjustment($adjustment1);
        $this->addAdjustment($adjustment2);

        $this->getTotal()->shouldReturn(1500);

        $this->clearAdjustments();
        $this->getTotal()->shouldReturn(1000);
    }

    function it_has_correct_total_after_neutral_adjustment_add_and_remove(
        AdjustmentInterface $adjustment,
        OrderItemInterface $orderItem
    ) {
        $orderItem->recalculateUnitsTotal()->shouldBeCalled();

        $adjustment->isNeutral()->willReturn(true);
        $adjustment->isRefund()->willReturn(true);
        $adjustment->getAmount()->willReturn(200);
        $adjustment->setAdjustable($this)->shouldBeCalled();

        $this->addAdjustment($adjustment);
        $this->getTotal()->shouldReturn(1000);

        $adjustment->setAdjustable(null)->shouldBeCalled();
        $adjustment->isLocked()->willReturn(false);

        $this->removeAdjustment($adjustment);
        $this->getTotal()->shouldReturn(1000);
    }

    function it_has_proper_total_after_order_item_unit_price_change(
        AdjustmentInterface $adjustment1,
        AdjustmentInterface $adjustment2,
        OrderItemInterface $orderItem
    ) {
        $orderItem->recalculateUnitsTotal()->shouldBeCalledTimes(2);

        $adjustment1->isNeutral()->willReturn(false);
        $adjustment1->isRefund()->willReturn(false);
        $adjustment1->setAdjustable($this)->shouldBeCalled();
        $adjustment1->getAmount()->willReturn(100);

        $adjustment2->isNeutral()->willReturn(false);
        $adjustment2->isRefund()->willReturn(false);
        $adjustment2->setAdjustable($this)->shouldBeCalled();
        $adjustment2->getAmount()->willReturn(50);

        $this->addAdjustment($adjustment1);
        $this->addAdjustment($adjustment2);

        $orderItem->getUnitPrice()->willReturn(500);

        $this->getTotal()->shouldReturn(650);
    }

    function it_recalculates_its_total_properly_after_adjustment_amount_change(
        AdjustmentInterface $adjustment1,
        AdjustmentInterface $adjustment2,
        OrderItemInterface $orderItem
    ) {
        $orderItem->recalculateUnitsTotal()->shouldBeCalledTimes(2);

        $adjustment1->isNeutral()->willReturn(false);
        $adjustment1->isRefund()->willReturn(false);
        $adjustment1->setAdjustable($this)->shouldBeCalled();
        $adjustment1->getAmount()->willReturn(100);

        $adjustment2->isNeutral()->willReturn(false);
        $adjustment2->isRefund()->willReturn(false);
        $adjustment2->setAdjustable($this)->shouldBeCalled();
        $adjustment2->getAmount()->willReturn(50);

        $this->addAdjustment($adjustment1);
        $this->addAdjustment($adjustment2);

        $adjustment2->getAmount()->willReturn(150);

        $this->recalculateAdjustmentsTotal();
        $this->getTotal()->shouldReturn(1250);
    }

    function it_calculates_correct_adjustments_refund_total(
        AdjustmentInterface $adjustment1,
        AdjustmentInterface $adjustment2,
        AdjustmentInterface $adjustment3,
        AdjustmentInterface $adjustment4,
        OrderItemInterface $orderItem
    ) {
        $orderItem->recalculateUnitsTotal()->shouldBeCalledTimes(4);

        $adjustment1->getAmount()->willReturn(25000);
        $adjustment2->getAmount()->willReturn(-4999);
        $adjustment3->getAmount()->willReturn(-2500);
        $adjustment4->getAmount()->willReturn(-10001);

        $adjustment1->isNeutral()->willReturn(false);
        $adjustment2->isNeutral()->willReturn(true);
        $adjustment3->isNeutral()->willReturn(false);
        $adjustment4->isNeutral()->willReturn(false);

        $adjustment1->isRefund()->willReturn(false);
        $adjustment2->isRefund()->willReturn(true);
        $adjustment3->isRefund()->willReturn(true);
        $adjustment4->isRefund()->willReturn(true);

        $adjustment1->setAdjustable($this)->shouldBeCalled();
        $adjustment2->setAdjustable($this)->shouldBeCalled();
        $adjustment3->setAdjustable($this)->shouldBeCalled();
        $adjustment4->setAdjustable($this)->shouldBeCalled();

        $this->addAdjustment($adjustment1);
        $this->addAdjustment($adjustment2);
        $this->addAdjustment($adjustment3);
        $this->addAdjustment($adjustment4);

        $this->getRefundAdjustmentsTotal()->shouldReturn(-12501);
    }

    function it_calculates_correct_adjustments_refund_total_by_type(
        AdjustmentInterface $adjustment1,
        AdjustmentInterface $adjustment2,
        AdjustmentInterface $adjustment3,
        AdjustmentInterface $adjustment4,
        OrderItemInterface $orderItem
    ) {
        $orderItem->recalculateUnitsTotal()->shouldBeCalledTimes(4);

        $adjustment1->getAmount()->willReturn(25000);
        $adjustment2->getAmount()->willReturn(-1999);
        $adjustment3->getAmount()->willReturn(-2500);
        $adjustment4->getAmount()->willReturn(-9999);

        $adjustment1->isNeutral()->willReturn(false);
        $adjustment2->isNeutral()->willReturn(true);
        $adjustment3->isNeutral()->willReturn(false);
        $adjustment4->isNeutral()->willReturn(false);

        $adjustment1->isRefund()->willReturn(false);
        $adjustment2->isRefund()->willReturn(true);
        $adjustment3->isRefund()->willReturn(true);
        $adjustment4->isRefund()->willReturn(true);

        $adjustment1->setAdjustable($this)->shouldBeCalled();
        $adjustment2->setAdjustable($this)->shouldBeCalled();
        $adjustment3->setAdjustable($this)->shouldBeCalled();
        $adjustment4->setAdjustable($this)->shouldBeCalled();

        $adjustment2->getType()->willReturn('tax');
        $adjustment3->getType()->willReturn('tax');
        $adjustment4->getType()->willReturn('promotion');

        $this->addAdjustment($adjustment1);
        $this->addAdjustment($adjustment2);
        $this->addAdjustment($adjustment3);
        $this->addAdjustment($adjustment4);

        $this->getRefundAdjustmentsTotal('tax')->shouldReturn(-2500);
    }

    function it_removes_refund_adjustments(
        AdjustmentInterface $adjustment1,
        AdjustmentInterface $adjustment2,
        AdjustmentInterface $adjustment3,
        OrderItemInterface $orderItem
    ) {
        $orderItem->recalculateUnitsTotal()->shouldBeCalledTimes(4);

        $adjustment1->getAmount()->willReturn(25000);
        $adjustment2->getAmount()->willReturn(-1999);
        $adjustment3->getAmount()->willReturn(-2500);

        $adjustment1->isNeutral()->willReturn(false);
        $adjustment2->isNeutral()->willReturn(false);
        $adjustment3->isNeutral()->willReturn(false);

        $adjustment1->isRefund()->willReturn(false);
        $adjustment2->isRefund()->willReturn(true);
        $adjustment3->isRefund()->willReturn(true);

        $adjustment1->setAdjustable($this)->shouldBeCalled();
        $adjustment2->setAdjustable($this)->shouldBeCalled();
        $adjustment3->setAdjustable($this)->shouldBeCalled();

        $adjustment2->getType()->willReturn('tax');
        $adjustment3->getType()->willReturn('tax');

        $this->addAdjustment($adjustment1);
        $this->addAdjustment($adjustment2);
        $this->addAdjustment($adjustment3);

        $this->getRefundAdjustments()->count()->shouldReturn(2);

        $adjustment3->isLocked()->willReturn(false);
        $adjustment3->setAdjustable(null)->shouldBeCalled();
        $this->removeAdjustment($adjustment3);

        $collection = $this->getRefundAdjustments();
        $collection->count()->shouldReturn(1);
        $collection->first()->shouldReturn($adjustment2);
    }
}
