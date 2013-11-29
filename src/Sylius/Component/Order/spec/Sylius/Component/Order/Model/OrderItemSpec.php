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
use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\Component\Order\Model\OrderInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderItemSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Order\Model\OrderItem');
    }

    function it_implements_Sylius_order_item_interface()
    {
        $this->shouldImplement('Sylius\Component\Order\Model\OrderItemInterface');
    }

    function it_implements_Sylius_adjustable_interface()
    {
        $this->shouldImplement('Sylius\Component\Order\Model\AdjustableInterface');
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

    function it_has_quantity_equal_to_1_by_default()
    {
        $this->getQuantity()->shouldReturn(1);
    }

    function its_quantity_is_mutable()
    {
        $this->setQuantity(8);
        $this->getQuantity()->shouldReturn(8);
    }

    function it_has_unit_price_equal_to_0_by_default()
    {
        $this->getUnitPrice()->shouldReturn(0);
    }

    function it_has_total_equal_to_0_by_default()
    {
        $this->getTotal()->shouldReturn(0);
    }

    function it_throws_exception_when_quantity_is_less_than_1()
    {
        $this
            ->shouldThrow(new \OutOfRangeException('Quantity must be greater than 0'))
            ->duringSetQuantity(-5)
        ;
    }

    function it_initializes_adjustments_collection_by_default()
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

    function its_total_is_mutable()
    {
        $this->setTotal(5999);
        $this->getTotal()->shouldReturn(5999);
    }

    function it_calculates_correct_total_based_on_quantity_and_unit_price()
    {
        $this->setQuantity(13);
        $this->setUnitPrice(1499);

        $this->calculateTotal();

        $this->getTotal()->shouldReturn(19487);
    }

    function it_calculates_correct_total_based_on_adjustments(AdjustmentInterface $adjustment)
    {
        $this->setQuantity(13);
        $this->setUnitPrice(1499);

        $adjustment->isNeutral()->willReturn(false);
        $adjustment->getAmount()->willReturn(-1000);
        $adjustment->setAdjustable($this)->shouldBeCalled();

        $this->addAdjustment($adjustment);

        $this->calculateTotal();

        $this->getTotal()->shouldReturn(18487);
    }

    function it_ignores_neutral_adjustments_when_calculating_total(AdjustmentInterface $adjustment, AdjustmentInterface $neutralAdjustment)
    {
        $this->setQuantity(13);
        $this->setUnitPrice(1499);

        $adjustment->isNeutral()->willReturn(false);
        $adjustment->getAmount()->willReturn(-1000);
        $adjustment->setAdjustable($this)->shouldBeCalled();
        $this->addAdjustment($adjustment);

        $neutralAdjustment->isNeutral()->willReturn(true);
        $neutralAdjustment->getAmount()->willReturn(2499);
        $neutralAdjustment->setAdjustable($this)->shouldBeCalled();
        $this->addAdjustment($neutralAdjustment);

        $this->calculateTotal();

        $this->getTotal()->shouldReturn(18487);
    }
}
