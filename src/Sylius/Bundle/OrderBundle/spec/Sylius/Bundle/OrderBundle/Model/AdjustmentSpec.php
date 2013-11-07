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

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class AdjustmentSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\OrderBundle\Model\Adjustment');
    }

    function it_implements_Sylius_adjustment_interface()
    {
        $this->shouldImplement('Sylius\Bundle\OrderBundle\Model\AdjustmentInterface');
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_does_not_belong_to_an_adjustable_by_default()
    {
        $this->getAdjustable()->shouldReturn(null);
    }

    /**
     * @param \Sylius\Bundle\OrderBundle\Model\OrderInterface     $order
     * @param \Sylius\Bundle\OrderBundle\Model\OrderItemInterface $orderItem
     */
    function it_allows_assigning_itself_to_an_adjustable($order, $orderItem)
    {
        $this->setAdjustable($order);
        $this->getAdjustable()->shouldReturn($order);

        $this->setAdjustable($orderItem);
        $this->getAdjustable()->shouldReturn($orderItem);
    }

    /**
     * @param \Sylius\Bundle\OrderBundle\Model\OrderInterface     $order
     * @param \Sylius\Bundle\OrderBundle\Model\OrderItemInterface $orderItem
     */
    function it_allows_detaching_itself_from_an_adjustable($order, $orderItem)
    {
        $this->setAdjustable($order);
        $this->getAdjustable()->shouldReturn($order);

        $this->setAdjustable(null);
        $this->getAdjustable()->shouldReturn(null);

        $this->setAdjustable($orderItem);
        $this->getAdjustable()->shouldReturn($orderItem);

        $this->setAdjustable(null);
        $this->getAdjustable()->shouldReturn(null);
    }

    function it_has_no_label_by_default()
    {
        $this->getLabel()->shouldReturn(null);
    }

    function its_label_is_mutable()
    {
        $this->setLabel('Shipping Fee');
        $this->getLabel()->shouldReturn('Shipping Fee');
    }

    function it_has_no_description_by_default()
    {
        $this->getDescription()->shouldReturn(null);
    }

    function its_description_is_mutable()
    {
        $this->setDescription('Clothing tax (12%)');
        $this->getDescription()->shouldReturn('Clothing tax (12%)');
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

    function it_is_a_charge_if_amount_is_lesser_than_0()
    {
        $this->setAmount(-4.99);
        $this->shouldBeCharge();

        $this->setAmount(6.99);
        $this->shouldNotBeCharge();
    }

    function it_is_a_credit_if_amount_is_greater_than_0()
    {
        $this->setAmount(29.99);
        $this->shouldBeCredit();

        $this->setAmount(-2.99);
        $this->shouldNotBeCredit();
    }

    function it_initializes_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType('DateTime');
    }

    function it_has_no_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    /**
     * @param \Sylius\Bundle\OrderBundle\Model\AdjustableInterface $adjustable
     */
    function it_has_fluent_interface($adjustable)
    {
        $this->setAdjustable($adjustable)->shouldReturn($this);
        $this->setLabel('Shipping fee')->shouldReturn($this);
        $this->setDescription('Tax (23%)')->shouldReturn($this);
        $this->setAmount(2.99)->shouldReturn($this);
        $this->setNeutral(true)->shouldReturn($this);
    }
}
