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
use Sylius\Component\Order\Model\IdentityInterface;
use Sylius\Component\Order\Model\OrderItemInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Order\Model\Order');
    }

    function it_implements_Sylius_order_interface()
    {
        $this->shouldImplement('Sylius\Component\Order\Model\OrderInterface');
    }

    function it_implements_Sylius_adjustable_interface()
    {
        $this->shouldImplement('Sylius\Component\Order\Model\AdjustableInterface');
    }

    function it_implements_Sylius_timestampable_interface()
    {
        $this->shouldImplement('Sylius\Component\Resource\Model\TimestampableInterface');
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_is_not_completed_by_default()
    {
        $this->shouldNotBeCompleted();
    }

    function it_can_be_completed()
    {
        $this->complete();
        $this->shouldBeCompleted();
    }

    function it_is_completed_when_completion_date_is_set()
    {
        $this->shouldNotBeCompleted();
        $this->setCompletedAt(new \DateTime('2 days ago'));
        $this->shouldBeCompleted();
    }

    function it_has_no_completion_date_by_default()
    {
        $this->getCompletedAt()->shouldReturn(null);
    }

    function its_completion_date_is_mutable()
    {
        $date = new \DateTime('1 hour ago');

        $this->setCompletedAt($date);
        $this->getCompletedAt()->shouldReturn($date);
    }

    function it_has_no_number_by_default()
    {
        $this->getNumber()->shouldReturn(null);
    }

    function its_number_is_mutable()
    {
        $this->setNumber('001351');
        $this->getNumber()->shouldReturn('001351');
    }

    function it_creates_items_collection_by_default()
    {
        $this->getItems()->shouldHaveType('Doctrine\\Common\\Collections\\Collection');
    }

    function it_creates_identities_collection_by_default()
    {
        $this->getIdentities()->shouldHaveType('Doctrine\\Common\\Collections\\Collection');
    }

    function it_adds_identities_properly(IdentityInterface $identity)
    {
        $this->hasIdentity($identity)->shouldReturn(false);

        $this->addIdentity($identity);
        $this->hasIdentity($identity)->shouldReturn(true);
    }

    function it_adds_items_properly(OrderItemInterface $item)
    {
        $this->hasItem($item)->shouldReturn(false);

        $this->addItem($item);
        $this->hasItem($item)->shouldReturn(true);
    }

    function it_removes_identities_properly(IdentityInterface $identity)
    {
        $this->hasIdentity($identity)->shouldReturn(false);

        $this->addIdentity($identity);
        $this->hasIdentity($identity)->shouldReturn(true);

        $this->removeIdentity($identity);
        $this->hasIdentity($identity)->shouldReturn(false);
    }

    function it_removes_items_properly(OrderItemInterface $item)
    {
        $this->hasItem($item)->shouldReturn(false);

        $this->addItem($item);
        $this->hasItem($item)->shouldReturn(true);

        $this->removeItem($item);
        $this->hasItem($item)->shouldReturn(false);
    }

    function it_has_items_total_equal_to_0_by_default()
    {
        $this->getItemsTotal()->shouldReturn(0);
    }

    function it_creates_adjustments_collection_by_default()
    {
        $this->getAdjustments()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    function it_adds_adjustments_properly(AdjustmentInterface $adjustment)
    {
        $adjustment->setAdjustable($this)->shouldBeCalled();

        $this->hasAdjustment($adjustment)->shouldReturn(false);
        $this->addAdjustment($adjustment);
        $this->hasAdjustment($adjustment)->shouldReturn(true);
    }

    function it_removes_adjustments_properly(AdjustmentInterface $adjustment)
    {
        $this->hasAdjustment($adjustment)->shouldReturn(false);

        $adjustment->setAdjustable($this)->shouldBeCalled();
        $this->addAdjustment($adjustment);

        $this->hasAdjustment($adjustment)->shouldReturn(true);

        $adjustment->isLocked()->willReturn(false);
        $adjustment->setAdjustable(null)->shouldBeCalled();
        $this->removeAdjustment($adjustment);

        $this->hasAdjustment($adjustment)->shouldReturn(false);
    }

    function it_has_total_equal_to_0_by_default()
    {
        $this->getTotal()->shouldReturn(0);
    }

    function it_initializes_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType('DateTime');
    }

    function it_has_no_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    function it_is_empty_by_default()
    {
        $this->countItems()->shouldReturn(0);
        $this->shouldBeEmpty();
    }

    function it_merges_equal_items(OrderItemInterface $item1, OrderItemInterface $item2)
    {
        $item1->setOrder($this)->shouldBeCalled();
        $item1->merge($item2, false)->shouldBeCalled();

        $item1->equals($item2)->willReturn(true);
        $item2->equals($item1)->willReturn(true);

        $this->addItem($item1);
        $this->addItem($item2);

        $this->countItems()->shouldReturn(1);
    }

    function it_should_be_able_to_clear_items(OrderItemInterface $item)
    {
        $this->shouldBeEmpty();
        $this->addItem($item);
        $this->countItems()->shouldReturn(1);
        $this->clearItems();
        $this->shouldBeEmpty();
    }

    function it_should_be_able_to_clear_adjustments(AdjustmentInterface $adjustment)
    {
        $this->hasAdjustment($adjustment)->shouldReturn(false);
        $this->addAdjustment($adjustment);
        $this->hasAdjustment($adjustment)->shouldReturn(true);

        $this->clearAdjustments();

        $this->hasAdjustment($adjustment)->shouldReturn(false);
    }

    function it_has_total_value_of_order(
        OrderItemInterface $orderItem1,
        OrderItemInterface $orderItem2
    ) {
        $this->getTotal()->shouldReturn(0);

        $orderItem1->getQuantity()->willReturn(2);
        $orderItem1->getUnitPrice()->willReturn(50);

        $orderItem1->setOrder($this)->shouldBeCalled();
        $this->addItem($orderItem1);

        $this->getTotal()->shouldReturn(100);

        $orderItem2->getQuantity()->willReturn(3);
        $orderItem2->getUnitPrice()->willReturn(100);

        $orderItem2->setOrder($this)->shouldBeCalled();
        $orderItem2->equals($orderItem1)->willReturn(false);
        $this->addItem($orderItem2);

        $this->getTotal()->shouldReturn(400);
    }

    function it_has_total_value_of_order_with_adjustments(
        OrderItemInterface $orderItem,
        AdjustmentInterface $adjustment
    ) {
        $this->getTotal()->shouldReturn(0);

        $orderItem->setOrder($this)->shouldBeCalled();
        $orderItem->getQuantity()->willReturn(1);
        $orderItem->getUnitPrice()->willReturn(100);

        $this->addItem($orderItem);

        $this->getTotal()->shouldReturn(100);

        $adjustment->isNeutral()->willReturn(false);
        $adjustment->getAmount()->willReturn(-25);
        $adjustment->setAdjustable($this)->shouldBeCalled();
        $this->addAdjustment($adjustment);

        $this->getTotal()->shouldReturn(75);
    }

    function it_has_never_negative_total_value(
        AdjustmentInterface $adjustment,
        OrderItemInterface $orderItem
    ) {
        $this->getTotal()->shouldReturn(0);

        $orderItem->setOrder($this)->shouldBeCalled();
        $orderItem->getQuantity()->willReturn(1);
        $orderItem->getUnitPrice()->willReturn(100);

        $this->addItem($orderItem);

        $this->getTotal()->shouldReturn(100);

        $adjustment->getAmount()->willReturn(-150);
        $adjustment->isNeutral()->willReturn(false);
        $adjustment->setAdjustable($this)->shouldBeCalled();

        $this->addAdjustment($adjustment);

        $this->getTotal()->shouldReturn(0);
    }

    function it_has_not_count_neutral_adjustments(
        AdjustmentInterface $adjustment1,
        OrderItemInterface $orderItem
    ) {
        $this->getTotal()->shouldReturn(0);

        $orderItem->setOrder($this)->shouldBeCalled();
        $orderItem->getQuantity()->willReturn(1);
        $orderItem->getUnitPrice()->willReturn(100);

        $this->addItem($orderItem);
        $this->getTotal()->shouldReturn(100);

        $adjustment1->getAmount()->willReturn(-150);
        $adjustment1->isNeutral()->willReturn(true);
        $adjustment1->setAdjustable($this)->shouldBeCalled();

        $this->addAdjustment($adjustment1);

        $this->getTotal()->shouldReturn(100);
    }
}
