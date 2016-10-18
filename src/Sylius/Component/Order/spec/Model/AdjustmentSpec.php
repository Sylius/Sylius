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
use Sylius\Component\Order\Model\AdjustableInterface;
use Sylius\Component\Order\Model\Adjustment;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Model\OrderItemUnitInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
final class AdjustmentSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Adjustment::class);
    }

    function it_implements_an_adjustment_interface()
    {
        $this->shouldImplement(AdjustmentInterface::class);
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_does_not_belong_to_an_adjustable_by_default()
    {
        $this->getAdjustable()->shouldReturn(null);
    }

    function it_allows_assigning_itself_to_an_adjustable(OrderInterface $order, OrderItemInterface $orderItem)
    {
        $order->addAdjustment($this->getWrappedObject())->shouldBeCalled();
        $this->setAdjustable($order);
        $this->getAdjustable()->shouldReturn($order);

        $order->removeAdjustment($this->getWrappedObject())->shouldBeCalled();
        $orderItem->addAdjustment($this->getWrappedObject())->shouldBeCalled();
        $this->setAdjustable($orderItem);
        $this->getAdjustable()->shouldReturn($orderItem);
    }

    function it_allows_detaching_itself_from_an_adjustable(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $orderItemUnit
    ) {
        $order->addAdjustment($this->getWrappedObject())->shouldBeCalled();
        $this->setAdjustable($order);
        $this->getAdjustable()->shouldReturn($order);

        $order->removeAdjustment($this->getWrappedObject())->shouldBeCalled();
        $this->setAdjustable(null);
        $this->getAdjustable()->shouldReturn(null);

        $orderItem->addAdjustment($this->getWrappedObject())->shouldBeCalled();
        $this->setAdjustable($orderItem);
        $this->getAdjustable()->shouldReturn($orderItem);

        $orderItem->removeAdjustment($this->getWrappedObject())->shouldBeCalled();
        $this->setAdjustable(null);
        $this->getAdjustable()->shouldReturn(null);

        $orderItemUnit->addAdjustment($this->getWrappedObject())->shouldBeCalled();
        $this->setAdjustable($orderItemUnit);
        $this->getAdjustable()->shouldReturn($orderItemUnit);

        $orderItemUnit->removeAdjustment($this->getWrappedObject())->shouldBeCalled();
        $this->setAdjustable(null);
        $this->getAdjustable()->shouldReturn(null);
    }

    function it_throws_an_exception_during_not_supported_adjustable_class_set(AdjustableInterface $adjustable)
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('setAdjustable', [$adjustable]);
    }

    function it_throws_an_exception_during_adjustable_change_on_locked_adjustment(
        OrderItemInterface $orderItem,
        OrderItemInterface $otherOrderItem
    ) {
        $this->setAdjustable($orderItem);
        $this->lock();
        $this->shouldThrow(\LogicException::class)->during('setAdjustable', [null]);
        $this->shouldThrow(\LogicException::class)->during('setAdjustable', [$otherOrderItem]);
    }

    function it_has_no_type_by_default()
    {
        $this->getType()->shouldReturn(null);
    }

    function its_type_is_mutable()
    {
        $this->setType('some type');
        $this->getType()->shouldReturn('some type');
    }

    function it_has_no_label_by_default()
    {
        $this->getLabel()->shouldReturn(null);
    }

    function its_label_is_mutable()
    {
        $this->setLabel('Clothing tax (12%)');
        $this->getLabel()->shouldReturn('Clothing tax (12%)');
    }

    function it_has_amount_equal_to_0_by_default()
    {
        $this->getAmount()->shouldReturn(0);
    }

    function its_amount_is_mutable()
    {
        $this->setAmount(399);
        $this->getAmount()->shouldReturn(399);
    }

    function it_recalculates_adjustments_on_adjustable_entity_on_amount_change(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $orderItemUnit
    ) {
        $order->addAdjustment($this->getWrappedObject())->shouldBeCalled();
        $this->setAdjustable($order);
        $order->recalculateAdjustmentsTotal()->shouldBeCalled();
        $this->setAmount(200);

        $order->removeAdjustment($this->getWrappedObject())->shouldBeCalled();
        $orderItem->addAdjustment($this->getWrappedObject())->shouldBeCalled();
        $this->setAdjustable($orderItem);
        $orderItem->recalculateAdjustmentsTotal()->shouldBeCalled();
        $this->setAmount(300);

        $orderItem->removeAdjustment($this->getWrappedObject())->shouldBeCalled();
        $orderItemUnit->addAdjustment($this->getWrappedObject())->shouldBeCalled();
        $this->setAdjustable($orderItemUnit);
        $orderItemUnit->recalculateAdjustmentsTotal()->shouldBeCalled();
        $this->setAmount(400);
    }

    function it_does_not_recalculate_adjustments_on_adjustable_entity_on_amount_change_when_adjustment_is_neutral(
        OrderItemUnitInterface $orderItemUnit
    ) {
        $orderItemUnit->addAdjustment($this->getWrappedObject())->shouldBeCalled();
        $this->setAdjustable($orderItemUnit);
        $orderItemUnit->recalculateAdjustmentsTotal()->shouldBeCalledTimes(1);
        $this->setNeutral(true);
        $this->setAmount(400);
    }

    function its_amount_should_accept_only_integer()
    {
        $this->setAmount(4498);
        $this->getAmount()->shouldBeInteger();
        $this->shouldThrow(\InvalidArgumentException::class)->during('setAmount', [44.98 * 100]);
        $this->shouldThrow(\InvalidArgumentException::class)->during('setAmount', ['4498']);
        $this->shouldThrow(\InvalidArgumentException::class)->during('setAmount', [round(44.98 * 100)]);
        $this->shouldThrow(\InvalidArgumentException::class)->during('setAmount', [[4498]]);
        $this->shouldThrow(\InvalidArgumentException::class)->during('setAmount', [new \stdClass()]);
    }

    function it_is_not_neutral_by_default()
    {
        $this->shouldNotBeNeutral();
    }

    function its_neutrality_is_mutable()
    {
        $this->shouldNotBeNeutral();
        $this->setNeutral(true);
        $this->shouldBeNeutral();
    }

    function it_recalculate_adjustments_on_adjustable_entity_on_neutral_change(OrderItemUnitInterface $orderItemUnit)
    {
        $orderItemUnit->addAdjustment($this->getWrappedObject())->shouldBeCalled();
        $this->setAdjustable($orderItemUnit);
        $orderItemUnit->recalculateAdjustmentsTotal()->shouldBeCalled();
        $this->setNeutral(true);
    }

    function it_does_not_recalculate_adjustments_on_adjustable_entity_when_neutral_set_to_current_value(
        OrderInterface $order
    ) {
        $order->addAdjustment($this->getWrappedObject())->shouldBeCalled();
        $this->setAdjustable($order);
        $order->recalculateAdjustmentsTotal()->shouldNotBeCalled();
        $this->setNeutral(false);
    }

    function it_is_a_charge_if_amount_is_lesser_than_0()
    {
        $this->setAmount(-499);
        $this->shouldBeCharge();

        $this->setAmount(699);
        $this->shouldNotBeCharge();
    }

    function it_is_a_credit_if_amount_is_greater_than_0()
    {
        $this->setAmount(2999);
        $this->shouldBeCredit();

        $this->setAmount(-299);
        $this->shouldNotBeCredit();
    }

    function its_origin_code_is_mutable()
    {
        $this->setOriginCode('TEST_PROMOTION');
        $this->getOriginCode()->shouldReturn('TEST_PROMOTION');
    }

    function it_initializes_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType(\DateTime::class);
    }

    function it_has_no_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }
}
