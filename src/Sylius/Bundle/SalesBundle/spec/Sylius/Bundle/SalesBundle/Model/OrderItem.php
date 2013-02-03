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

use PHPSpec2\ObjectBehavior;

/**
 * Order item spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderItem extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SalesBundle\Model\OrderItem');
    }

    function it_should_implement_Sylius_order_item_interface()
    {
        $this->shouldImplement('Sylius\Bundle\SalesBundle\Model\OrderItemInterface');
    }

    function it_should_implement_Sylius_adjustable_interface()
    {
        $this->shouldImplement('Sylius\Bundle\SalesBundle\Model\AdjustableInterface');
    }

    function it_should_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_should_not_belong_to_an_order_by_default()
    {
        $this->getOrder()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\SalesBundle\Model\OrderInterface $order
     */
    function it_should_allow_assigning_itself_to_an_order($order)
    {
        $this->setOrder($order);
        $this->getOrder()->shouldReturn($order);
    }

    /**
     * @param Sylius\Bundle\SalesBundle\Model\OrderInterface $order
     */
    function it_should_allow_detaching_itself_from_an_order($order)
    {
        $this->setOrder($order);
        $this->getOrder()->shouldReturn($order);

        $this->setOrder(null);
        $this->getOrder()->shouldReturn(null);
    }

    function it_should_not_have_sellable_defined_by_default()
    {
        $this->getSellable()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\SalesBundle\Model\SellableInterface $sellable
     */
    function it_should_allow_defining_sellable($sellable)
    {
        $this->setSellable($sellable);
        $this->getSellable()->shouldReturn($sellable);
    }

    function it_should_have_quantity_equal_to_1_by_default()
    {
        $this->getQuantity()->shouldReturn(1);
    }

    function its_quantity_should_be_mutable()
    {
        $this->setQuantity(8);
        $this->getQuantity()->shouldReturn(8);
    }

    function it_should_have_unit_price_equal_to_0_by_default()
    {
        $this->getUnitPrice()->shouldReturn(0);
    }

    function it_should_have_total_equal_to_0_by_default()
    {
        $this->getTotal()->shouldReturn(0);
    }

    function it_should_complain_when_quantity_is_less_than_1()
    {
        $this
            ->shouldThrow(new \OutOfRangeException('Quantity must be greater than 0'))
            ->duringSetQuantity(-5)
        ;
    }

    function it_should_initialize_adjustments_collection_by_default()
    {
        $this->getAdjustments()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    /**
     * @param Sylius\Bundle\SalesBundle\Model\AdjustmentInterface $adjustment
     */
    function it_should_add_adjustments_properly($adjustment)
    {
        $adjustment->setAdjustable($this)->shouldBeCalled();

        $this->hasAdjustment($adjustment)->shouldReturn(false);
        $this->addAdjustment($adjustment);
        $this->hasAdjustment($adjustment)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\SalesBundle\Model\AdjustmentInterface $adjustment
     */
    function it_should_remove_adjustments_properly($adjustment)
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
     * @param Sylius\Bundle\SalesBundle\Model\AdjustmentInterface $adjustment
     */
    function it_should_have_fluent_interface_for_adjustments_management($adjustment)
    {
        $this->addAdjustment($adjustment)->shouldReturn($this);
        $this->removeAdjustment($adjustment)->shouldReturn($this);
    }


    function its_total_should_be_mutable()
    {
        $this->setTotal(59.99);
        $this->getTotal()->shouldReturn(59.99);
    }

    function it_should_calculate_correct_total_based_on_quantity_and_unit_price()
    {
        $this->setQuantity(13);
        $this->setUnitPrice(14.99);

        $this->calculateTotal();

        $this->getTotal()->shouldReturn(194.87);
    }

    /**
     * @param Sylius\Bundle\SalesBundle\Model\AdjustmentInterface $adjustment
     */
    function it_should_calculate_correct_total_based_on_adjustments($adjustment)
    {
        $this->setQuantity(13);
        $this->setUnitPrice(14.99);

        $adjustment->isNeutral()->willReturn(false);
        $adjustment->getAmount()->willReturn(-10);
        $this->addAdjustment($adjustment);

        $this->calculateTotal();

        $this->getTotal()->shouldReturn(184.87);
    }

    /**
     * @param Sylius\Bundle\SalesBundle\Model\AdjustmentInterface $adjustment
     * @param Sylius\Bundle\SalesBundle\Model\AdjustmentInterface $neutralAdjustment
     */
    function it_should_ignore_neutral_adjustments_when_calculating_total($adjustment, $neutralAdjustment)
    {
        $this->setQuantity(13);
        $this->setUnitPrice(14.99);

        $adjustment->isNeutral()->willReturn(false);
        $adjustment->getAmount()->willReturn(-10);
        $this->addAdjustment($adjustment);

        $neutralAdjustment->isNeutral()->willReturn(true);
        $neutralAdjustment->getAmount()->willReturn(24.99);
        $this->addAdjustment($neutralAdjustment);

        $this->calculateTotal();

        $this->getTotal()->shouldReturn(184.87);
    }
}
