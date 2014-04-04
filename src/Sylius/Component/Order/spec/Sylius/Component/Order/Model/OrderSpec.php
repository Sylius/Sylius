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

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
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

    function it_adds_items_properly(OrderItemInterface $item)
    {
        $this->hasItem($item)->shouldReturn(false);

        $this->addItem($item);
        $this->hasItem($item)->shouldReturn(true);
    }

    function it_removes_items_properly(OrderItemInterface $item)
    {
        $this->hasItem($item)->shouldReturn(false);

        $this->addItem($item);
        $this->hasItem($item)->shouldReturn(true);

        $this->removeItem($item);
        $this->hasItem($item)->shouldReturn(false);
    }

    function it_has_fluent_interface_for_items_management(OrderItemInterface $item)
    {
        $this->addItem($item)->shouldReturn($this);
        $this->removeItem($item)->shouldReturn($this);

        $this->clearItems()->shouldReturn($this);
    }

    function it_has_items_total_equal_to_0_by_default()
    {
        $this->getItemsTotal()->shouldReturn(0);
    }

    function it_calculates_correct_items_total(OrderItemInterface $item1, OrderItemInterface $item2, OrderItemInterface $item3)
    {
        $item1->calculateTotal()->shouldBeCalled();
        $item2->calculateTotal()->shouldBeCalled();
        $item3->calculateTotal()->shouldBeCalled();

        $item1->getTotal()->willReturn(29999);
        $item2->getTotal()->willReturn(45000);
        $item3->getTotal()->willReturn(250);

        $item1->setOrder($this)->shouldBeCalled();
        $item2->setOrder($this)->shouldBeCalled();
        $item3->setOrder($this)->shouldBeCalled();

        $item1->equals(Argument::any())->willReturn(false);
        $item2->equals(Argument::any())->willReturn(false);
        $item3->equals(Argument::any())->willReturn(false);

        $this
            ->addItem($item1)
            ->addItem($item2)
            ->addItem($item3)
        ;

        $this->calculateItemsTotal();

        $this->getItemsTotal()->shouldReturn(75249);
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

        $adjustment->setAdjustable(null)->shouldBeCalled();
        $this->removeAdjustment($adjustment);

        $this->hasAdjustment($adjustment)->shouldReturn(false);
    }

    function it_has_fluent_interface_for_adjustments_management(AdjustmentInterface $adjustment)
    {
        $this->addAdjustment($adjustment)->shouldReturn($this);
        $this->removeAdjustment($adjustment)->shouldReturn($this);
    }

    function it_has_adjustments_total_equal_to_0_by_default()
    {
        $this->getAdjustmentsTotal()->shouldReturn(0);
    }

    function it_calculates_correct_adjustments_total(AdjustmentInterface $adjustment1, AdjustmentInterface $adjustment2, AdjustmentInterface $adjustment3)
    {
        $adjustment1->getAmount()->willReturn(10000);
        $adjustment2->getAmount()->willReturn(-4999);
        $adjustment3->getAmount()->willReturn(1929);

        $adjustment1->isNeutral()->willReturn(false);
        $adjustment2->isNeutral()->willReturn(false);
        $adjustment3->isNeutral()->willReturn(false);

        $adjustment1->setAdjustable($this)->shouldBeCalled();
        $adjustment2->setAdjustable($this)->shouldBeCalled();
        $adjustment3->setAdjustable($this)->shouldBeCalled();

        $this
            ->addAdjustment($adjustment1)
            ->addAdjustment($adjustment2)
            ->addAdjustment($adjustment3)
        ;

        $this->calculateAdjustmentsTotal();

        $this->getAdjustmentsTotal()->shouldReturn(6930);
    }

    function it_has_total_equal_to_0_by_default()
    {
        $this->getTotal()->shouldReturn(0);
    }

    function it_calculates_correct_total(OrderItemInterface $item1, OrderItemInterface $item2, AdjustmentInterface $adjustment1, AdjustmentInterface $adjustment2)
    {
        $item1->calculateTotal()->shouldBeCalled();
        $item2->calculateTotal()->shouldBeCalled();

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

        $this
            ->addItem($item1)
            ->addItem($item2)
            ->addAdjustment($adjustment1)
            ->addAdjustment($adjustment2)
        ;

        $this->calculateTotal();

        $this->getTotal()->shouldReturn(80000);
    }

    function it_ignores_neutral_adjustments_when_calculating_total(OrderItemInterface $item1, OrderItemInterface $item2, AdjustmentInterface $adjustment1, AdjustmentInterface $adjustment2)
    {
        $item1->calculateTotal()->shouldBeCalled();
        $item2->calculateTotal()->shouldBeCalled();

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

        $this
            ->addItem($item1)
            ->addItem($item2)
            ->addAdjustment($adjustment1)
            ->addAdjustment($adjustment2)
        ;

        $this->calculateTotal();

        $this->getTotal()->shouldReturn(70000);
    }

    function it_calculates_correct_total_when_adjustment_is_bigger_than_cost(OrderItemInterface $item, AdjustmentInterface $adjustment)
    {
        $item->calculateTotal()->shouldBeCalled();

        $item->getTotal()->willReturn(45000);

        $item->equals(Argument::any())->willReturn(false);

        $item->setOrder($this)->shouldBeCalled();

        $adjustment->isNeutral()->willReturn(false);
        $adjustment->getAmount()->willReturn(-100000);

        $adjustment->setAdjustable($this)->shouldBeCalled();

        $this
            ->addItem($item)
            ->addAdjustment($adjustment)
        ;

        $this->calculateTotal();

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

    function it_merges_equal_items(OrderItemInterface $item1, OrderItemInterface$item2)
    {
        $item1->setOrder($this)->shouldBeCalled();

        $item1->equals($item2)->willReturn(true);
        $item2->equals($item1)->willReturn(true);
        $item1->merge($item2, false)->willReturn($this)->shouldBeCalled();

        $this
            ->addItem($item1)
            ->addItem($item2)
        ;

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
}
