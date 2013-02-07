<?php

namespace spec\Sylius\Bundle\PromotionsBundle\Checker;

use PHPSpec2\ObjectBehavior;
use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Bundle\PromotionsBundle\Model\RuleInterface;

/**
 * Item count rule checker spec.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ItemCountRuleChecker extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PromotionsBundle\Checker\ItemCountRuleChecker');
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
        $order->countItems()->shouldBeCalled()->willReturn(0);

        $this->isEligible($order, array('count' => 10, 'equal' => false))->shouldReturn(false);
    }

    /**
     * @param Sylius\Bundle\SalesBundle\Model\OrderInterface $order
     */
    function it_should_recognize_order_as_not_eligible_if_item_count_is_less_then_configured($order)
    {
        $order->countItems()->shouldBeCalled()->willReturn(7);

        $this->isEligible($order, array('count' => 10, 'equal' => false))->shouldReturn(false);
    }

    /**
     * @param Sylius\Bundle\SalesBundle\Model\OrderInterface $order
     */
    function it_should_recognize_order_as_eligible_if_item_count_is_greater_then_configured($order)
    {
        $order->countItems()->shouldBeCalled()->willReturn(12);

        $this->isEligible($order, array('count' => 10, 'equal' => false))->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\SalesBundle\Model\OrderInterface $order
     */
    function it_should_recognize_order_as_eligible_if_item_count_is_equal_with_configured_depending_on_equal_setting($order)
    {
        $order->countItems()->shouldBeCalled()->willReturn(10);

        $this->isEligible($order, array('count' => 10, 'equal' => false))->shouldReturn(false);
        $this->isEligible($order, array('count' => 10, 'equal' => true))->shouldReturn(true);
    }
}
