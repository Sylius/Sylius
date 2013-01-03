<?php

namespace spec\Sylius\Bundle\SalesBundle\Model;

use PHPSpec2\ObjectBehavior;

/**
 * Total adjustment model spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Adjustment extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SalesBundle\Model\Adjustment');
    }

    function it_should_be_a_Sylius_sales_adjustment()
    {
        $this->shouldImplement('Sylius\Bundle\SalesBundle\Model\AdjustmentInterface');
    }

    function it_should_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_should_not_belong_to_an_adjustable_by_default()
    {
        $this->getAdjustable()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\SalesBundle\Model\OrderInterface     $order
     * @param Sylius\Bundle\SalesBundle\Model\OrderItemInterface $orderItem
     */
    function it_should_allow_assigning_itself_to_an_adjustable($order, $orderItem)
    {
        $this->setAdjustable($order);
        $this->getAdjustable()->shouldReturn($order);

        $this->setAdjustable($orderItem);
        $this->getAdjustable()->shouldReturn($orderItem);
    }

    /**
     * @param Sylius\Bundle\SalesBundle\Model\OrderInterface     $order
     * @param Sylius\Bundle\SalesBundle\Model\OrderItemInterface $orderItem
     */
    function it_should_allow_detaching_itself_from_an_adjustable($order, $orderItem)
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

    function it_should_not_have_label_by_default()
    {
        $this->getLabel()->shouldReturn(null);
    }

    function its_label_should_be_mutable()
    {
        $this->setLabel('Shipping Fee');
        $this->getLabel()->shouldReturn('Shipping Fee');
    }

    function it_should_not_have_description_by_default()
    {
        $this->getDescription()->shouldReturn(null);
    }

    function its_description_should_be_mutable()
    {
        $this->setDescription('Clothing tax (12%)');
        $this->getDescription()->shouldReturn('Clothing tax (12%)');
    }

    function it_should_have_amount_equal_to_0_by_default()
    {
        $this->getAmount()->shouldReturn(0);
    }

    function its_amount_should_be_mutable()
    {
        $this->setAmount(399);
        $this->getAmount()->shouldReturn(399);
    }

    function it_should_not_be_neutral_by_default()
    {
        $this->shouldNotBeNeutral();
    }

    function its_neutrality_should_be_mutable()
    {
        $this->shouldNotBeNeutral();
        $this->setNeutral(true);
        $this->shouldBeNeutral();
    }

    function it_should_be_a_charge_if_amount_is_lesser_than_0()
    {
        $this->setAmount(-4.99);
        $this->shouldBeCharge();

        $this->setAmount(6.99);
        $this->shouldNotBeCharge();
    }

    function it_should_be_a_credit_if_amount_is_greater_than_0()
    {
        $this->setAmount(29.99);
        $this->shouldBeCredit();

        $this->setAmount(-2.99);
        $this->shouldNotBeCredit();
    }

    function it_should_initialize_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType('DateTime');
    }

    function it_should_not_have_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\SalesBundle\Model\AdjustableInterface $adjustable
     */
    function it_should_have_fluid_interface($adjustable)
    {
        $this->setAdjustable($adjustable)->shouldReturn($this);
        $this->setLabel('Shipping fee')->shouldReturn($this);
        $this->setAmount(2.99)->shouldReturn($this);
    }
}
