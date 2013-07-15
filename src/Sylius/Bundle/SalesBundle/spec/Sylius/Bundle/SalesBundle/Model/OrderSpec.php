<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\SalesBundle\Model;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SalesBundle\Model\Order');
    }

    function it_implements_Sylius_order_interface()
    {
        $this->shouldImplement('Sylius\Bundle\SalesBundle\Model\OrderInterface');
    }

    function it_implements_Sylius_adjustable_interface()
    {
        $this->shouldImplement('Sylius\Bundle\SalesBundle\Model\AdjustableInterface');
    }

    function it_implements_Sylius_timestampable_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ResourceBundle\Model\TimestampableInterface');
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

    /**
     * @param \Sylius\Bundle\SalesBundle\Model\OrderItemInterface $item
     */
    function it_adds_items_properly($item)
    {
        $this->hasItem($item)->shouldReturn(false);

        $this->addItem($item);
        $this->hasItem($item)->shouldReturn(true);
    }

    /**
     * @param \Sylius\Bundle\SalesBundle\Model\OrderItemInterface $item
     */
    function it_removes_items_properly($item)
    {
        $this->hasItem($item)->shouldReturn(false);

        $this->addItem($item);
        $this->hasItem($item)->shouldReturn(true);

        $this->removeItem($item);
        $this->hasItem($item)->shouldReturn(false);
    }

    /**
     * @param \Sylius\Bundle\SalesBundle\Model\OrderItemInterface $item
     */
    function it_has_fluent_interface_for_items_management($item)
    {
        $this->addItem($item)->shouldReturn($this);
        $this->removeItem($item)->shouldReturn($this);

        $this->clearItems()->shouldReturn($this);
    }

    function it_has_items_total_equal_to_0_by_default()
    {
        $this->getItemsTotal()->shouldReturn(0);
    }

    /**
     * @param \Sylius\Bundle\SalesBundle\Model\OrderItemInterface $item1
     * @param \Sylius\Bundle\SalesBundle\Model\OrderItemInterface $item2
     * @param \Sylius\Bundle\SalesBundle\Model\OrderItemInterface $item3
     */
    function it_calculates_correct_items_total($item1, $item2, $item3)
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

    /**
     * @param \Sylius\Bundle\SalesBundle\Model\AdjustmentInterface $adjustment
     */
    function it_adds_adjustments_properly($adjustment)
    {
        $adjustment->setAdjustable($this)->shouldBeCalled();

        $this->hasAdjustment($adjustment)->shouldReturn(false);
        $this->addAdjustment($adjustment);
        $this->hasAdjustment($adjustment)->shouldReturn(true);
    }

    /**
     * @param \Sylius\Bundle\SalesBundle\Model\AdjustmentInterface $adjustment
     */
    function it_removes_adjustments_properly($adjustment)
    {
        $this->hasAdjustment($adjustment)->shouldReturn(false);

        $adjustment->setAdjustable($this)->shouldBeCalled();
        $this->addAdjustment($adjustment);

        $this->hasAdjustment($adjustment)->shouldReturn(true);

        $adjustment->setAdjustable(null)->shouldBeCalled();
        $this->removeAdjustment($adjustment);

        $this->hasAdjustment($adjustment)->shouldReturn(false);
    }

    /**
     * @param \Sylius\Bundle\SalesBundle\Model\AdjustmentInterface $adjustment
     */
    function it_has_fluent_interface_for_adjustments_management($adjustment)
    {
        $this->addAdjustment($adjustment)->shouldReturn($this);
        $this->removeAdjustment($adjustment)->shouldReturn($this);
    }

    function it_has_adjustments_total_equal_to_0_by_default()
    {
        $this->getAdjustmentsTotal()->shouldReturn(0);
    }

    /**
     * @param \Sylius\Bundle\SalesBundle\Model\AdjustmentInterface $adjustment1
     * @param \Sylius\Bundle\SalesBundle\Model\AdjustmentInterface $adjustment2
     * @param \Sylius\Bundle\SalesBundle\Model\AdjustmentInterface $adjustment3
     */
    function it_calculates_correct_adjustments_total($adjustment1, $adjustment2, $adjustment3)
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

    /**
     * @param \Sylius\Bundle\SalesBundle\Model\OrderItemInterface $item1
     * @param \Sylius\Bundle\SalesBundle\Model\OrderItemInterface $item2
     * @param \Sylius\Bundle\SalesBundle\Model\AdjustmentInterface $adjustment1
     * @param \Sylius\Bundle\SalesBundle\Model\AdjustmentInterface $adjustment2
     */
    function it_calculates_correct_total($item1, $item2, $adjustment1, $adjustment2)
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

    /**
     * @param \Sylius\Bundle\SalesBundle\Model\OrderItemInterface $item1
     * @param \Sylius\Bundle\SalesBundle\Model\OrderItemInterface $item2
     * @param \Sylius\Bundle\SalesBundle\Model\AdjustmentInterface $adjustment1
     * @param \Sylius\Bundle\SalesBundle\Model\AdjustmentInterface $adjustment2
     */
    function it_ignores_neutral_adjustments_when_calculating_total($item1, $item2, $adjustment1, $adjustment2)
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

    function it_is_not_confirmed_by_default()
    {
        $this->shouldNotBeConfirmed();
    }

    function its_confirmation_status_is_activable()
    {
        $this->setConfirmed(true);
        $this->isConfirmed()->shouldReturn(true);
    }

    function its_confirmation_status_is_mutable()
    {
        $this->setConfirmed(false);
        $this->isConfirmed()->shouldReturn(false);
    }

    function it_has_no_confirmation_token_by_default()
    {
        $this->getConfirmationToken()->shouldReturn(null);
    }

    function its_confirmation_token_is_mutable()
    {
        $this->setConfirmationToken('abc123');
        $this->getConfirmationToken()->shouldReturn('abc123');
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

    /**
     * @param Sylius\Bundle\SalesBundle\Model\OrderItemInterface $item1
     * @param Sylius\Bundle\SalesBundle\Model\OrderItemInterface $item2
     */
    function it_sums_the_quantities_of_equal_items($item1, $item2)
    {
        $item1->getQuantity()->willReturn(3);
        $item2->getQuantity()->willReturn(7);

        $item1->setOrder($this)->shouldBeCalled();
        $item1->setQuantity(10)->shouldBeCalled();

        $item2->equals($item1)->willReturn(true);

        $this
            ->addItem($item1)
            ->addItem($item2)
        ;

        $this->countItems()->shouldReturn(1);
    }
}
