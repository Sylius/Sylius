<?php

namespace spec\Sylius\Component\Promotion\Checker;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

class ItemMostExpensiveRuleCheckerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Promotion\Checker\ItemMostExpensiveRuleChecker');
    }

    function it_should_be_a_rule_checker()
    {
        $this->shouldHaveType('Sylius\Component\Promotion\Checker\RuleCheckerInterface');
    }

    function it_should_recognize_empty_cart_as_not_eligible(OrderInterface $subject)
    {
        $this->shouldThrow('Sylius\Component\Promotion\Exception\UnsupportedTypeException')->during('isEligible', [$subject, []]);
    }

    function it_should_recognize_one_item_as_eligible(OrderItemInterface $subject, OrderInterface $order)
    {
        $subject->getOrder()->willReturn($order);
        $order->getItems()->willReturn([$subject]);
        $subject->getUnitPrice()->willReturn(100);
        $this->isEligible($subject, [])->shouldReturn(true);
    }

    function it_should_recognize_most_expensive_item_as_eligible(OrderItemInterface $subject, OrderItemInterface $otherItem, OrderInterface $order)
    {
        $subject->getOrder()->willReturn($order);
        $order->getItems()->willReturn([$subject, $otherItem]);
        $subject->getUnitPrice()->willReturn(100);
        $otherItem->getUnitPrice()->willReturn(50);

        $this->isEligible($subject, [])->shouldReturn(true);
    }

    function it_should_recognize_least_expensive_item_as_eligible(OrderItemInterface $subject, OrderItemInterface $otherItem, OrderInterface $order)
    {
        $subject->getOrder()->willReturn($order);
        $order->getItems()->willReturn([$subject, $otherItem]);
        $subject->getUnitPrice()->willReturn(50);
        $otherItem->getUnitPrice()->willReturn(100);

        $this->isEligible($subject, [])->shouldReturn(false);
    }
}
