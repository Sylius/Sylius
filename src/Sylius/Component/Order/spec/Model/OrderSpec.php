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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Order\Model\AdjustableInterface;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

final class OrderSpec extends ObjectBehavior
{
    function it_implements_an_order_interface(): void
    {
        $this->shouldImplement(OrderInterface::class);
    }

    function it_implements_an_adjustable_interface(): void
    {
        $this->shouldImplement(AdjustableInterface::class);
    }

    function it_implements_a_timestampable_interface(): void
    {
        $this->shouldImplement(TimestampableInterface::class);
    }

    function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function it_does_not_have_completed_checkout_by_default(): void
    {
        $this->shouldNotBeCheckoutCompleted();
    }

    function its_checkout_can_be_completed(): void
    {
        $this->completeCheckout();
        $this->shouldBeCheckoutCompleted();
    }

    function it_has_checkout_completed_when_completion_date_is_set(): void
    {
        $this->shouldNotBeCheckoutCompleted();
        $this->setCheckoutCompletedAt(new \DateTime('2 days ago'));
        $this->shouldBeCheckoutCompleted();
    }

    function it_has_no_checkout_completion_date_by_default(): void
    {
        $this->getCheckoutCompletedAt()->shouldReturn(null);
    }

    function its_checkout_completion_date_is_mutable(): void
    {
        $date = new \DateTime('1 hour ago');

        $this->setCheckoutCompletedAt($date);
        $this->getCheckoutCompletedAt()->shouldReturn($date);
    }

    function it_has_no_number_by_default(): void
    {
        $this->getNumber()->shouldReturn(null);
    }

    function its_number_is_mutable(): void
    {
        $this->setNumber('001351');
        $this->getNumber()->shouldReturn('001351');
    }

    function it_creates_items_collection_by_default(): void
    {
        $this->getItems()->shouldHaveType(Collection::class);
    }

    function it_adds_items_properly(OrderItemInterface $item): void
    {
        $item->getTotal()->willReturn(420);
        $item->setOrder($this)->shouldBeCalled();

        $this->hasItem($item)->shouldReturn(false);

        $this->addItem($item);
        $this->hasItem($item)->shouldReturn(true);
    }

    function it_removes_items_properly(OrderItemInterface $item): void
    {
        $item->getTotal()->willReturn(420);

        $item->setOrder($this)->shouldBeCalled();
        $this->addItem($item);

        $item->setOrder(null)->shouldBeCalled();
        $this->removeItem($item);

        $this->hasItem($item)->shouldReturn(false);
    }

    function it_has_items_total_equal_to_0_by_default(): void
    {
        $this->getItemsTotal()->shouldReturn(0);
    }

    function it_calculates_correct_items_total(
        OrderItemInterface $item1,
        OrderItemInterface $item2,
        OrderItemInterface $item3
    ): void {
        $item1->getTotal()->willReturn(29999);
        $item2->getTotal()->willReturn(45000);
        $item3->getTotal()->willReturn(250);

        $item1->setOrder($this)->shouldBeCalled();
        $item2->setOrder($this)->shouldBeCalled();
        $item3->setOrder($this)->shouldBeCalled();

        $item1->equals(Argument::any())->willReturn(false);
        $item2->equals(Argument::any())->willReturn(false);
        $item3->equals(Argument::any())->willReturn(false);

        $this->addItem($item1);
        $this->addItem($item2);
        $this->addItem($item3);

        $this->getItemsTotal()->shouldReturn(75249);
    }

    function it_creates_adjustments_collection_by_default(): void
    {
        $this->getAdjustments()->shouldHaveType(Collection::class);
    }

    function it_adds_adjustments_properly(AdjustmentInterface $adjustment): void
    {
        $adjustment->setAdjustable($this)->shouldBeCalled();
        $adjustment->isNeutral()->willReturn(true);

        $this->hasAdjustment($adjustment)->shouldReturn(false);
        $this->addAdjustment($adjustment);
        $this->hasAdjustment($adjustment)->shouldReturn(true);
    }

    function it_removes_adjustments_properly(AdjustmentInterface $adjustment): void
    {
        $this->hasAdjustment($adjustment)->shouldReturn(false);

        $adjustment->setAdjustable($this)->shouldBeCalled();
        $adjustment->isNeutral()->willReturn(true);

        $this->addAdjustment($adjustment);
        $this->hasAdjustment($adjustment)->shouldReturn(true);

        $adjustment->isLocked()->willReturn(false);
        $adjustment->setAdjustable(null)->shouldBeCalled();
        $this->removeAdjustment($adjustment);

        $this->hasAdjustment($adjustment)->shouldReturn(false);
    }

    function it_removes_adjustments_recursively_properly(
        AdjustmentInterface $orderAdjustment,
        OrderItemInterface $item
    ): void {
        $orderAdjustment->getAmount()->willReturn(420);
        $orderAdjustment->isNeutral()->willReturn(true);
        $orderAdjustment->isLocked()->willReturn(false);
        $orderAdjustment->setAdjustable(Argument::any())->will(function () {});

        $item->getTotal()->willReturn(666);
        $item->setOrder($this)->will(function () {});

        $this->addAdjustment($orderAdjustment);
        $this->addItem($item);

        $item->removeAdjustmentsRecursively(null)->shouldBeCalled();

        $this->removeAdjustmentsRecursively();

        $this->hasAdjustment($orderAdjustment)->shouldReturn(false);
    }

    function it_removes_adjustments_recursively_by_type_properly(
        AdjustmentInterface $orderPromotionAdjustment,
        AdjustmentInterface $orderTaxAdjustment,
        OrderItemInterface $item
    ): void {
        $orderPromotionAdjustment->getType()->willReturn('promotion');
        $orderPromotionAdjustment->isNeutral()->willReturn(true);
        $orderPromotionAdjustment->isLocked()->willReturn(false);
        $orderPromotionAdjustment->setAdjustable($this)->shouldBeCalled();
        $orderPromotionAdjustment->getAmount()->willReturn(420);
        $orderPromotionAdjustment->setAdjustable(Argument::any())->will(function () {});

        $orderTaxAdjustment->getType()->willReturn('tax');
        $orderTaxAdjustment->isNeutral()->willReturn(true);
        $orderTaxAdjustment->isLocked()->willReturn(false);
        $orderTaxAdjustment->setAdjustable($this)->shouldBeCalled();
        $orderTaxAdjustment->getAmount()->willReturn(420);
        $orderTaxAdjustment->setAdjustable(Argument::any())->will(function () {});

        $item->getTotal()->willReturn(666);
        $item->setOrder($this)->will(function () {});

        $this->addAdjustment($orderPromotionAdjustment);
        $this->addAdjustment($orderTaxAdjustment);
        $this->addItem($item);

        $item->removeAdjustmentsRecursively('tax')->shouldBeCalled();
        $orderTaxAdjustment->setAdjustable(null)->shouldBeCalled();

        $this->removeAdjustmentsRecursively('tax');

        $this->hasAdjustment($orderPromotionAdjustment)->shouldReturn(true);
        $this->hasAdjustment($orderTaxAdjustment)->shouldReturn(false);
    }

    function it_returns_adjustments_recursively(
        AdjustmentInterface $orderAdjustment,
        AdjustmentInterface $itemAdjustment1,
        AdjustmentInterface $itemAdjustment2,
        OrderItemInterface $item1,
        OrderItemInterface $item2
    ): void {
        $item1->setOrder($this)->shouldBeCalled();
        $item1->getTotal()->willReturn(100);
        $item1->getAdjustmentsRecursively(null)->willReturn(new ArrayCollection([$itemAdjustment1->getWrappedObject()]));

        $item2->setOrder($this)->shouldBeCalled();
        $item2->getTotal()->willReturn(100);
        $item2->getAdjustmentsRecursively(null)->willReturn(new ArrayCollection([$itemAdjustment2->getWrappedObject()]));

        $this->addItem($item1);
        $this->addItem($item2);

        $orderAdjustment->setAdjustable($this)->shouldBeCalled();
        $orderAdjustment->isNeutral()->willReturn(true);

        $this->addAdjustment($orderAdjustment);

        $this->getAdjustmentsRecursively()->shouldIterateAs([$orderAdjustment, $itemAdjustment1, $itemAdjustment2]);
    }

    function it_has_adjustments_total_equal_to_0_by_default(): void
    {
        $this->getAdjustmentsTotal()->shouldReturn(0);
    }

    function it_calculates_correct_adjustments_total(
        AdjustmentInterface $adjustment1,
        AdjustmentInterface $adjustment2,
        AdjustmentInterface $adjustment3
    ): void {
        $adjustment1->getAmount()->willReturn(10000);
        $adjustment2->getAmount()->willReturn(-4999);
        $adjustment3->getAmount()->willReturn(1929);

        $adjustment1->isNeutral()->willReturn(false);
        $adjustment2->isNeutral()->willReturn(false);
        $adjustment3->isNeutral()->willReturn(false);

        $adjustment1->setAdjustable($this)->shouldBeCalled();
        $adjustment2->setAdjustable($this)->shouldBeCalled();
        $adjustment3->setAdjustable($this)->shouldBeCalled();

        $this->addAdjustment($adjustment1);
        $this->addAdjustment($adjustment2);
        $this->addAdjustment($adjustment3);

        $this->getAdjustmentsTotal()->shouldReturn(6930);
    }

    function it_returns_adjustments_total_recursively(
        AdjustmentInterface $itemAdjustment,
        AdjustmentInterface $orderAdjustment,
        OrderItemInterface $orderItem
    ): void {
        $itemAdjustment->getAmount()->willReturn(10000);
        $orderAdjustment->getAmount()->willReturn(5000);

        $itemAdjustment->isNeutral()->willReturn(false);
        $orderAdjustment->isNeutral()->willReturn(false);

        $orderAdjustment->setAdjustable($this)->shouldBeCalled();

        $orderItem->getAdjustmentsRecursively(null)->willReturn(new ArrayCollection([$itemAdjustment->getWrappedObject()]));
        $orderItem->setOrder($this)->shouldBeCalled();
        $orderItem->getTotal()->willReturn(15000);

        $this->addItem($orderItem);
        $this->addAdjustment($orderAdjustment);

        $this->getAdjustmentsTotalRecursively()->shouldReturn(15000);
    }

    function it_has_total_equal_to_0_by_default(): void
    {
        $this->getTotal()->shouldReturn(0);
    }

    function it_has_total_quantity(OrderItemInterface $orderItem1, OrderItemInterface $orderItem2): void
    {
        $orderItem1->getQuantity()->willReturn(10);
        $orderItem1->setOrder($this)->shouldBeCalled();
        $orderItem1->getTotal()->willReturn(500);

        $orderItem2->getQuantity()->willReturn(30);
        $orderItem2->setOrder($this)->shouldBeCalled();
        $orderItem2->equals($orderItem1)->willReturn(false);
        $orderItem2->getTotal()->willReturn(1000);

        $this->addItem($orderItem1);
        $this->addItem($orderItem2);

        $this->getTotalQuantity()->shouldReturn(40);
    }

    function it_calculates_correct_total(
        OrderItemInterface $item1,
        OrderItemInterface $item2,
        AdjustmentInterface $adjustment1,
        AdjustmentInterface $adjustment2
    ): void {
        $item1->getTotal()->willReturn(29999);
        $item2->getTotal()->willReturn(45000);

        $item1->equals(Argument::any())->willReturn(false);
        $item2->equals(Argument::any())->willReturn(false);

        $item1->setOrder($this)->shouldBeCalled();
        $item2->setOrder($this)->shouldBeCalled();

        $adjustment1->isNeutral()->willReturn(false);
        $adjustment1->getAmount()->willReturn(10000);
        $adjustment2->isNeutral()->willReturn(false);
        $adjustment2->getAmount()->willReturn(-4999);

        $adjustment1->setAdjustable($this)->shouldBeCalled();
        $adjustment2->setAdjustable($this)->shouldBeCalled();

        $this->addItem($item1);
        $this->addItem($item2);
        $this->addAdjustment($adjustment1);
        $this->addAdjustment($adjustment2);

        $this->getTotal()->shouldReturn(80000);
    }

    function it_calculates_correct_total_after_items_and_adjustments_changes(
        AdjustmentInterface $adjustment1,
        AdjustmentInterface $adjustment2,
        OrderItemInterface $item1,
        OrderItemInterface $item2,
        OrderItemInterface $item3
    ): void {
        $item1->getTotal()->willReturn(29999);
        $item2->getTotal()->willReturn(45000);

        $item1->equals(Argument::any())->willReturn(false);
        $item2->equals(Argument::any())->willReturn(false);

        $item1->setOrder($this)->shouldBeCalled();
        $item2->setOrder($this)->shouldBeCalled();

        $adjustment1->isNeutral()->willReturn(false);
        $adjustment1->getAmount()->willReturn(10000);
        $adjustment1->setAdjustable($this)->shouldBeCalled();

        $this->addItem($item1);
        $this->addItem($item2);
        $this->addAdjustment($adjustment1);

        $this->getTotal()->shouldReturn(84999);

        $item2->setOrder(null)->shouldBeCalled();

        $this->removeItem($item2);

        $adjustment1->setAdjustable(null)->shouldBeCalled();
        $adjustment1->isLocked()->willReturn(false);

        $adjustment2->isNeutral()->willReturn(false);
        $adjustment2->getAmount()->willReturn(-4999);
        $adjustment2->setAdjustable($this)->shouldBeCalled();

        $this->removeAdjustment($adjustment1);
        $this->addAdjustment($adjustment2);

        $item3->getTotal()->willReturn(55000);
        $item3->equals(Argument::any())->willReturn(false);
        $item3->setOrder($this)->shouldBeCalled();

        $this->addItem($item3);

        $this->getTotal()->shouldReturn(80000);
    }

    function it_ignores_neutral_adjustments_when_calculating_total(
        OrderItemInterface $item1,
        OrderItemInterface $item2,
        AdjustmentInterface $adjustment1,
        AdjustmentInterface $adjustment2
    ): void {
        $item1->getTotal()->willReturn(29999);
        $item2->getTotal()->willReturn(45000);

        $item1->equals(Argument::any())->willReturn(false);
        $item2->equals(Argument::any())->willReturn(false);

        $item1->setOrder($this)->shouldBeCalled();
        $item2->setOrder($this)->shouldBeCalled();

        $adjustment1->isNeutral()->willReturn(true);
        $adjustment1->getAmount()->willReturn(10000);
        $adjustment2->isNeutral()->willReturn(false);
        $adjustment2->getAmount()->willReturn(-4999);

        $adjustment1->setAdjustable($this)->shouldBeCalled();
        $adjustment2->setAdjustable($this)->shouldBeCalled();

        $this->addItem($item1);
        $this->addItem($item2);
        $this->addAdjustment($adjustment1);
        $this->addAdjustment($adjustment2);

        $this->getTotal()->shouldReturn(70000);
    }

    function it_calculates_correct_total_when_adjustment_is_bigger_than_cost(
        OrderItemInterface $item,
        AdjustmentInterface $adjustment
    ): void {
        $item->getTotal()->willReturn(45000);

        $item->equals(Argument::any())->willReturn(false);

        $item->setOrder($this)->shouldBeCalled();

        $adjustment->isNeutral()->willReturn(false);
        $adjustment->getAmount()->willReturn(-100000);

        $adjustment->setAdjustable($this)->shouldBeCalled();

        $this->addItem($item);
        $this->addAdjustment($adjustment);

        $this->getTotal()->shouldReturn(0);
    }

    function it_initializes_creation_date_by_default(): void
    {
        $this->getCreatedAt()->shouldHaveType(\DateTimeInterface::class);
    }

    function it_has_no_last_update_date_by_default(): void
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    function it_is_empty_by_default(): void
    {
        $this->countItems()->shouldReturn(0);
        $this->isEmpty()->shouldReturn(true);
    }

    function it_clears_items(OrderItemInterface $item): void
    {
        $item->getTotal()->willReturn(420);
        $item->setOrder($this)->will(function () {});

        $this->addItem($item);
        $this->clearItems();

        $this->isEmpty()->shouldReturn(true);
        $this->getTotal()->shouldReturn(0);
    }

    function it_has_notes(): void
    {
        $this->setNotes('something squishy');
        $this->getNotes()->shouldReturn('something squishy');
    }
}
