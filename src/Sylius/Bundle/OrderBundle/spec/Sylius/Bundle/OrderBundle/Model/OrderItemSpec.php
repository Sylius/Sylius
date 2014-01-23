<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\OrderBundle\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\OrderBundle\Model\OrderInterface;
use Sylius\Bundle\OrderBundle\Model\OrderItemInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderItemSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\OrderBundle\Model\OrderItem');
    }

    function it_implements_Sylius_order_item_interface()
    {
        $this->shouldImplement('Sylius\Bundle\OrderBundle\Model\OrderItemInterface');
    }

    function it_implements_Sylius_adjustable_interface()
    {
        $this->shouldImplement('Sylius\Bundle\OrderBundle\Model\AdjustableInterface');
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

    /**
     * @param Sylius\Bundle\OrderBundle\Model\AdjustmentInterface $adjustment
     */
    function it_adds_adjustments_properly($adjustment)
    {
        $adjustment->setAdjustable($this)->shouldBeCalled();

        $this->hasAdjustment($adjustment)->shouldReturn(false);
        $this->addAdjustment($adjustment);
        $this->hasAdjustment($adjustment)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\OrderBundle\Model\AdjustmentInterface $adjustment
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
     * @param Sylius\Bundle\OrderBundle\Model\AdjustmentInterface $adjustment
     */
    function it_has_fluent_interface_for_adjustments_management($adjustment)
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

    /**
     * @param Sylius\Bundle\OrderBundle\Model\AdjustmentInterface $adjustment
     */
    function it_calculates_correct_total_based_on_adjustments($adjustment)
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

    /**
     * @param Sylius\Bundle\OrderBundle\Model\AdjustmentInterface $adjustment
     * @param Sylius\Bundle\OrderBundle\Model\AdjustmentInterface $neutralAdjustment
     */
    function it_ignores_neutral_adjustments_when_calculating_total($adjustment, $neutralAdjustment)
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

    function it_ignores_merging_same_items()
    {
        $this->merge($this);
        $this->getQuantity()->shouldReturn(1);
    }

    function it_merges_an_equal_item_by_summing_quantities(OrderItemInterface $item)
    {
        $this->setQuantity(3);

        $item->getQuantity()->willReturn(7);
        $item->equals($this)->willReturn(true);

        $this->merge($item);
        $this->getQuantity()->shouldReturn(10);
    }

    function it_merges_a_known_equal_item_without_calling_equals(OrderItemInterface $item)
    {
        $this->setQuantity(3);

        $item->getQuantity()->willReturn(7);
        $item->equals($this)->shouldNotBeCalled();

        $this->merge($item, false);
        $this->getQuantity()->shouldReturn(10);
    }

    function it_throws_exception_when_merging_unequal_item(OrderItemInterface $item)
    {
        $item->equals($this)->willReturn(false);

        $this
            ->shouldThrow(new \RuntimeException('Given item cannot be merged.'))
            ->duringMerge($item);
    }
}
