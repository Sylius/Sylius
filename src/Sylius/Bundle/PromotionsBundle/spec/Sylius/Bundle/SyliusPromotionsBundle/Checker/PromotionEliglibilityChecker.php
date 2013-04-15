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
     * @param Sylius\Bundle\PromotionsBundle\Model\PromotionSubjectInterface          $subject
     * @param Sylius\Bundle\PromotionsBundle\Model\PromotionInterface $promotion
     * @param Sylius\Bundle\PromotionsBundle\Model\RuleInterface      $rule
     */
    function it_should_recognize_subject_as_eligible_if_all_checkers_recognize_it_as_eligible($registry, $checker, $subject, $promotion, $rule)
    {
        $configuration = array();

        $registry->getChecker(RuleInterface::TYPE_ITEM_TOTAL)->shouldBeCalled()->willReturn($checker);
        $promotion->getRules()->shouldBeCalled()->willReturn(array($rule));
        $rule->getType()->shouldBeCalled()->willReturn(RuleInterface::TYPE_ITEM_TOTAL);
        $rule->getConfiguration()->shouldBeCalled()->willReturn($configuration);

        $checker->isEligible($subject, $configuration)->shouldBeCalled()->willReturn(true);

        $this->isEligible($subject, $promotion)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\PromotionsBundle\Model\PromotionSubjectInterface          $subject
     * @param Sylius\Bundle\PromotionsBundle\Model\PromotionInterface $promotion
     * @param Sylius\Bundle\PromotionsBundle\Model\RuleInterface      $rule
     */
    function it_should_recognize_subject_as_not_eligible_if_any_checker_recognize_it_as_not_eligible($registry, $checker, $subject, $promotion, $rule)
    {
        $configuration = array();

        $registry->getChecker(RuleInterface::TYPE_ITEM_TOTAL)->shouldBeCalled()->willReturn($checker);
        $promotion->getRules()->shouldBeCalled()->willReturn(array($rule));
        $rule->getType()->shouldBeCalled()->willReturn(RuleInterface::TYPE_ITEM_TOTAL);
        $rule->getConfiguration()->shouldBeCalled()->willReturn($configuration);

        $checker->isEligible($subject, $configuration)->shouldBeCalled()->willReturn(false);

        $this->isEligible($subject, $promotion)->shouldReturn(false);
    }

    /**
     * @param Sylius\Bundle\PromotionsBundle\Model\CouponAwarePromotionSubjectInterface $subject
     * @param Sylius\Bundle\PromotionsBundle\Model\PromotionInterface        $promotion
     */
    function it_should_recognize_subject_as_eligible_if_promotion_have_no_coupon_codes($registry, $subject, $promotion)
    {
        $configuration = array();

        $promotion->getRules()->shouldBeCalled()->willReturn(array());
        $promotion->hasCoupons()->shouldBeCalled()->willReturn(false);

        $this->isEligible($subject, $promotion)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\PromotionsBundle\Model\CouponAwarePromotionSubjectInterface $subject
     * @param Sylius\Bundle\PromotionsBundle\Model\PromotionInterface        $promotion
     * @param Sylius\Bundle\PromotionsBundle\Model\CouponInterface           $coupon
     */
    function it_should_recognize_subject_as_not_eligible_if_coupon_code_does_not_match($registry, $subject, $promotion, $coupon)
    {
        $configuration = array();

        $subject->getCoupon()->shouldBeCalled()->willReturn($coupon);
        $promotion->getRules()->shouldBeCalled()->willReturn(array());
        $promotion->hasCoupons()->shouldBeCalled()->willReturn(true);
        $promotion->hasCoupon($coupon)->shouldBeCalled()->willReturn(false);

        $this->isEligible($subject, $promotion)->shouldReturn(false);
    }

    /**
     * @param Sylius\Bundle\PromotionsBundle\Model\CouponAwarePromotionSubjectInterface $subject
     * @param Sylius\Bundle\PromotionsBundle\Model\PromotionInterface        $promotion
     * @param Sylius\Bundle\PromotionsBundle\Model\CouponInterface           $coupon
     */
    function it_should_recognize_subject_as_eligible_if_coupon_code_match($registry, $subject, $promotion, $coupon)
    {
        $configuration = array();

        $subject->getCoupon()->shouldBeCalled()->willReturn($coupon);
        $promotion->getRules()->shouldBeCalled()->willReturn(array());
        $promotion->hasCoupons()->shouldBeCalled()->willReturn(true);
        $promotion->hasCoupon($coupon)->shouldBeCalled()->willReturn(true);

        $this->isEligible($subject, $promotion)->shouldReturn(true);
    }
}
