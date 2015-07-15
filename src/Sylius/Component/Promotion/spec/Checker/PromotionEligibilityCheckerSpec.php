<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Promotion\Checker;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Promotion\Checker\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\CouponInterface;
use Sylius\Component\Promotion\Model\PromotionCouponsAwareSubjectInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Promotion\Model\RuleInterface;
use Sylius\Component\Promotion\SyliusPromotionEvents;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class PromotionEligibilityCheckerSpec extends ObjectBehavior
{
    public function let(ServiceRegistryInterface $registry, EventDispatcherInterface $dispatcher)
    {
        $this->beConstructedWith($registry, $dispatcher);
    }

    public function it_is_a_rule_checker()
    {
        $this->shouldBeAnInstanceOf('Sylius\Component\Promotion\Checker\PromotionEligibilityCheckerInterface');
    }

    public function it_recognizes_subject_as_eligible_if_all_checkers_recognize_it_as_eligible(
        $registry,
        RuleCheckerInterface $checker,
        PromotionCouponsAwareSubjectInterface $subject,
        PromotionInterface $promotion,
        RuleInterface $rule
    ) {
        $promotion->getStartsAt()->willReturn(null);
        $promotion->getEndsAt()->willReturn(null);
        $promotion->getUsageLimit()->willReturn(null);
        $promotion->hasRules()->willReturn(true);
        $registry->get(RuleInterface::TYPE_ITEM_TOTAL)->willReturn($checker);
        $promotion->getRules()->willReturn(array($rule));
        $promotion->isCouponBased()->willReturn(false);

        $rule->getType()->willReturn(RuleInterface::TYPE_ITEM_TOTAL);
        $rule->getConfiguration()->willReturn(array());

        $registry->get(RuleInterface::TYPE_ITEM_TOTAL)->willReturn($checker);
        $checker->isEligible($subject, array())->willReturn(true);

        $subject->getPromotionCoupons()->willReturn(array());

        $this->isEligible($subject, $promotion)->shouldReturn(true);
    }

    public function it_recognizes_subject_as_not_eligible_if_any_checker_recognize_it_as_not_eligible(
        $registry,
        RuleCheckerInterface $checker,
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion,
        RuleInterface $rule
    ) {
        $promotion->getStartsAt()->willReturn(null);
        $promotion->getEndsAt()->willReturn(null);
        $promotion->getUsageLimit()->willReturn(null);
        $promotion->hasRules()->willReturn(true);
        $registry->get(RuleInterface::TYPE_ITEM_TOTAL)->willReturn($checker);
        $promotion->getRules()->willReturn(array($rule));
        $promotion->isCouponBased()->willReturn(false);

        $rule->getType()->willReturn(RuleInterface::TYPE_ITEM_TOTAL);
        $rule->getConfiguration()->willReturn(array());

        $registry->get(RuleInterface::TYPE_ITEM_TOTAL)->willReturn($checker);
        $checker->isEligible($subject, array())->willReturn(false);

        $this->isEligible($subject, $promotion)->shouldReturn(false);
    }

    public function it_recognizes_subject_as_not_eligible_if_any_checker_recognize_it_as_not_eligible_even_when_coupon_matches(
        $registry,
        RuleCheckerInterface $checker,
        PromotionCouponsAwareSubjectInterface $subject,
        PromotionInterface $promotion,
        CouponInterface $coupon,
        RuleInterface $rule
    ) {
        $promotion->getStartsAt()->willReturn(null);
        $promotion->getEndsAt()->willReturn(null);
        $promotion->getUsageLimit()->willReturn(null);
        $promotion->hasRules()->willReturn(true);
        $registry->get(RuleInterface::TYPE_ITEM_TOTAL)->willReturn($checker);
        $promotion->getRules()->willReturn(array($rule));
        $promotion->isCouponBased()->willReturn(true);

        $rule->getType()->willReturn(RuleInterface::TYPE_ITEM_TOTAL);
        $rule->getConfiguration()->willReturn(array());

        $registry->get(RuleInterface::TYPE_ITEM_TOTAL)->willReturn($checker);
        $checker->isEligible($subject, array())->willReturn(false);

        $subject->getPromotionCoupons()->willReturn(array($coupon));
        $coupon->getPromotion()->willReturn($promotion);

        $this->isEligible($subject, $promotion)->shouldReturn(false);
    }

    public function it_recognizes_subject_as_eligible_if_promotion_have_no_coupon_codes(
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion
    ) {
        $promotion->getStartsAt()->willReturn(null);
        $promotion->getEndsAt()->willReturn(null);
        $promotion->getUsageLimit()->willReturn(null);
        $promotion->hasRules()->willReturn(false);
        $promotion->isCouponBased()->willReturn(false);

        $this->isEligible($subject, $promotion)->shouldReturn(true);
    }

    public function it_recognizes_subject_as_not_eligible_if_coupon_code_does_not_match(
        PromotionCouponsAwareSubjectInterface $subject,
        PromotionInterface $promotion,
        CouponInterface $coupon
    ) {
        $subject->getPromotionCoupons()->willReturn(array($coupon));

        $promotion->getStartsAt()->willReturn(null);
        $promotion->getEndsAt()->willReturn(null);
        $promotion->getUsageLimit()->willReturn(null);
        $promotion->hasRules()->willReturn(false);
        $promotion->isCouponBased()->willReturn(true);
        $coupon->getPromotion()->willReturn(null);

        $this->isEligible($subject, $promotion)->shouldReturn(false);
    }

    public function it_recognizes_subject_as_not_eligible_if_promotion_subject_is_not_coupon_aware(
        $registry,
        RuleCheckerInterface $checker,
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion,
        CouponInterface $coupon,
        RuleInterface $rule
    ) {
        $promotion->getStartsAt()->willReturn(null);
        $promotion->getEndsAt()->willReturn(null);
        $promotion->getUsageLimit()->willReturn(null);
        $promotion->hasRules()->willReturn(true);
        $promotion->isCouponBased()->willReturn(true);
        $coupon->getPromotion()->willReturn($promotion);

        $registry->get(RuleInterface::TYPE_ITEM_TOTAL)->willReturn($checker);
        $promotion->getRules()->willReturn(array($rule));

        $rule->getType()->willReturn(RuleInterface::TYPE_ITEM_TOTAL);
        $rule->getConfiguration()->willReturn(array());

        $registry->get(RuleInterface::TYPE_ITEM_TOTAL)->willReturn($checker);
        $checker->isEligible($subject, array())->willReturn(false);

        $this->isEligible($subject, $promotion)->shouldReturn(false);
    }

    public function it_recognizes_subject_as_eligible_if_coupon_code_match(
        $dispatcher,
        PromotionCouponsAwareSubjectInterface $subject,
        PromotionInterface $promotion,
        CouponInterface $coupon
    ) {
        $subject->getPromotionCoupons()->willReturn(array($coupon));

        $promotion->getStartsAt()->willReturn(null);
        $promotion->getEndsAt()->willReturn(null);
        $promotion->getUsageLimit()->willReturn(null);
        $promotion->hasRules()->willReturn(false);
        $promotion->isCouponBased()->willReturn(true);
        $coupon->getPromotion()->willReturn($promotion);

        $dispatcher->dispatch(SyliusPromotionEvents::COUPON_ELIGIBLE, Argument::any())->shouldBeCalled();

        $this->isEligible($subject, $promotion)->shouldReturn(true);
    }
}
