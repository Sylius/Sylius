<?php

namespace spec\Sylius\Bundle\PromotionsBundle\Checker;

use PHPSpec2\ObjectBehavior;
use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Bundle\PromotionsBundle\Model\RuleInterface;

/**
 * Order total rule checker spec.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class OrderTotalRuleChecker extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PromotionsBundle\Checker\OrderTotalRuleChecker');
    }

    function it_should_be_Sylius_rule_checker()
    {
        $this->shouldImplement('Sylius\Bundle\PromotionsBundle\Checker\RuleCheckerInterface');
    }

    /**
     * @param Sylius\Bundle\SalesBundle\Model\OrderInterface $order
     */
    function it_should_recognize_empty_order_as_not_eligible($order)
    {
        $order->getTotal()->shouldBeCalled()->willReturn(0);

        $this->isEligible($order, array('value' => 500, 'equal' => false))->shouldReturn(false);
    }

    /**
     * @param Sylius\Bundle\SalesBundle\Model\OrderInterface $order
     */
    function it_should_recognize_order_as_not_eligible_if_order_total_is_less_then_configured($order)
    {
        $order->getTotal()->shouldBeCalled()->willReturn(400);

        $this->isEligible($order, array('value' => 500, 'equal' => false))->shouldReturn(false);
    }

    /**
     * @param Sylius\Bundle\SalesBundle\Model\OrderInterface $order
     */
    function it_should_recognize_order_as_eligible_if_order_total_is_greater_then_configured($order)
    {
        $order->getTotal()->shouldBeCalled()->willReturn(600);

        $this->isEligible($order, array('value' => 500, 'equal' => false))->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\SalesBundle\Model\OrderInterface $order
     */
    function it_should_recognize_order_as_eligible_if_order_total_is_equal_with_configured_depending_on_equal_setting($order)
    {
        $order->getTotal()->shouldBeCalled()->willReturn(500);

        $this->isEligible($order, array('value' => 500, 'equal' => false))->shouldReturn(false);
        $this->isEligible($order, array('value' => 500, 'equal' => true))->shouldReturn(true);
    }
}
