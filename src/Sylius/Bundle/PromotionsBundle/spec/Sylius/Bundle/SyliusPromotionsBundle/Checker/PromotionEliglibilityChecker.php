<?php

namespace spec\Sylius\Bundle\PromotionsBundle\Checker;

use PHPSpec2\ObjectBehavior;
use Sylius\Bundle\PromotionsBundle\Model\RuleInterface;

/**
 * Rule checker spec.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class PromotionEliglibilityChecker extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\PromotionsBundle\Checker\Registry\RuleCheckerRegistryInterface $registry
     * @param Sylius\Bundle\PromotionsBundle\Checker\RuleCheckerInterface                  $checker
     */
    function let($registry, $checker)
    {
        $this->beConstructedWith($registry);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PromotionsBundle\Checker\PromotionEliglibilityChecker');
    }

    function it_should_be_Sylius_rule_checker()
    {
        $this->shouldImplement('Sylius\Bundle\PromotionsBundle\Checker\PromotionEliglibilityCheckerInterface');
    }

    /**
     * @param Sylius\Bundle\SalesBundle\Model\OrderInterface          $order
     * @param Sylius\Bundle\PromotionsBundle\Model\PromotionInterface $promotion
     * @param Sylius\Bundle\PromotionsBundle\Model\RuleInterface      $rule
     */
    function it_should_recognize_order_as_eligible_if_all_checkers_recognize_it_as_eligible($registry, $checker, $order, $promotion, $rule)
    {
        $configuration = array();

        $registry->getChecker(RuleInterface::TYPE_ORDER_TOTAL)->shouldBeCalled()->willReturn($checker);
        $promotion->getRules()->shouldBeCalled()->willReturn(array($rule));
        $rule->getType()->shouldBeCalled()->willReturn(RuleInterface::TYPE_ORDER_TOTAL);
        $rule->getConfiguration()->shouldBeCalled()->willReturn($configuration);

        $checker->isEligible($order, $configuration)->shouldBeCalled()->willReturn(true);

        $this->isEligible($order, $promotion)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\SalesBundle\Model\OrderInterface          $order
     * @param Sylius\Bundle\PromotionsBundle\Model\PromotionInterface $promotion
     * @param Sylius\Bundle\PromotionsBundle\Model\RuleInterface      $rule
     */
    function it_should_recognize_order_as_not_eligible_if_any_checker_recognize_it_as_not_eligible($registry, $checker, $order, $promotion, $rule)
    {
        $configuration = array();

        $registry->getChecker(RuleInterface::TYPE_ORDER_TOTAL)->shouldBeCalled()->willReturn($checker);
        $promotion->getRules()->shouldBeCalled()->willReturn(array($rule));
        $rule->getType()->shouldBeCalled()->willReturn(RuleInterface::TYPE_ORDER_TOTAL);
        $rule->getConfiguration()->shouldBeCalled()->willReturn($configuration);

        $checker->isEligible($order, $configuration)->shouldBeCalled()->willReturn(false);

        $this->isEligible($order, $promotion)->shouldReturn(false);
    }
}
