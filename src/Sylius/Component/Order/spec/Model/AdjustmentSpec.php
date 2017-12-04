<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Order\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Order\Model\AdjustableInterface;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Model\OrderItemUnitInterface;

final class AdjustmentSpec extends ObjectBehavior
{
    function it_implements_an_adjustment_interface(): void
    {
        $this->shouldImplement(AdjustmentInterface::class);
    }

    function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function it_does_not_belong_to_an_adjustable_by_default(): void
    {
        $this->getAdjustable()->shouldReturn(null);
    }

    function it_allows_assigning_itself_to_an_adjustable(OrderInterface $order, OrderItemInterface $orderItem): void
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
    ): void {
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

    function it_throws_an_exception_during_not_supported_adjustable_class_set(AdjustableInterface $adjustable): void
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('setAdjustable', [$adjustable]);
    }

    function it_throws_an_exception_during_adjustable_change_on_locked_adjustment(
        OrderItemInterface $orderItem,
        OrderItemInterface $otherOrderItem
    ): void {
        $this->setAdjustable($orderItem);
        $this->lock();
        $this->shouldThrow(\LogicException::class)->during('setAdjustable', [null]);
        $this->shouldThrow(\LogicException::class)->during('setAdjustable', [$otherOrderItem]);
    }

    function it_has_no_type_by_default(): void
    {
        $this->getType()->shouldReturn(null);
    }

    function its_type_is_mutable(): void
    {
        $this->setType('some type');
        $this->getType()->shouldReturn('some type');
    }

    function it_has_no_label_by_default(): void
    {
        $this->getLabel()->shouldReturn(null);
    }

    function its_label_is_mutable(): void
    {
        $this->setLabel('Clothing tax (12%)');
        $this->getLabel()->shouldReturn('Clothing tax (12%)');
    }

    function it_has_amount_equal_to_0_by_default(): void
    {
        $this->getAmount()->shouldReturn(0);
    }

    function its_amount_is_mutable(): void
    {
        $this->setAmount(399);
        $this->getAmount()->shouldReturn(399);
    }

    function it_recalculates_adjustments_on_adjustable_entity_on_amount_change(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $orderItemUnit
    ): void {
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
    ): void {
        $orderItemUnit->addAdjustment($this->getWrappedObject())->shouldBeCalled();
        $this->setAdjustable($orderItemUnit);
        $orderItemUnit->recalculateAdjustmentsTotal()->shouldBeCalledTimes(1);
        $this->setNeutral(true);
        $this->setAmount(400);
    }

    function its_amount_should_accept_only_integer(): void
    {
        $this->setAmount(4498);
        $this->getAmount()->shouldReturn(4498);
    }

    function it_is_not_neutral_by_default(): void
    {
        $this->shouldNotBeNeutral();
    }

    function its_neutrality_is_mutable(): void
    {
        $this->shouldNotBeNeutral();
        $this->setNeutral(true);
        $this->shouldBeNeutral();
    }

    function it_recalculate_adjustments_on_adjustable_entity_on_neutral_change(OrderItemUnitInterface $orderItemUnit): void
    {
        $orderItemUnit->addAdjustment($this->getWrappedObject())->shouldBeCalled();
        $this->setAdjustable($orderItemUnit);
        $orderItemUnit->recalculateAdjustmentsTotal()->shouldBeCalled();
        $this->setNeutral(true);
    }

    function it_does_not_recalculate_adjustments_on_adjustable_entity_when_neutral_set_to_current_value(
        OrderInterface $order
    ): void {
        $order->addAdjustment($this->getWrappedObject())->shouldBeCalled();
        $this->setAdjustable($order);
        $order->recalculateAdjustmentsTotal()->shouldNotBeCalled();
        $this->setNeutral(false);
    }

    function it_is_a_charge_if_amount_is_lesser_than_0(): void
    {
        $this->setAmount(-499);
        $this->shouldBeCharge();

        $this->setAmount(699);
        $this->shouldNotBeCharge();
    }

    function it_is_a_credit_if_amount_is_greater_than_0(): void
    {
        $this->setAmount(2999);
        $this->shouldBeCredit();

        $this->setAmount(-299);
        $this->shouldNotBeCredit();
    }

    function its_origin_code_is_mutable(): void
    {
        $this->setOriginCode('TEST_PROMOTION');
        $this->getOriginCode()->shouldReturn('TEST_PROMOTION');
    }

    function it_initializes_creation_date_by_default(): void
    {
        $this->getCreatedAt()->shouldHaveType(\DateTimeInterface::class);
    }

    function it_has_no_last_update_date_by_default(): void
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }
}
