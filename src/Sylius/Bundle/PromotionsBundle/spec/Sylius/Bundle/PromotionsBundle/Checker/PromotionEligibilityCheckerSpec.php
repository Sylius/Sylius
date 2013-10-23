<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PromotionsBundle\Checker;

use PhpSpec\ObjectBehavior;

use Sylius\Bundle\PromotionsBundle\Checker\Registry\RuleCheckerRegistryInterface;
use Sylius\Bundle\PromotionsBundle\Checker\RuleCheckerInterface;
use Sylius\Bundle\PromotionsBundle\Model\CouponInterface;
use Sylius\Bundle\PromotionsBundle\Model\PromotionSubjectInterface;
use Sylius\Bundle\PromotionsBundle\Model\PromotionInterface;
use Sylius\Bundle\PromotionsBundle\Model\RuleInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class PromotionEligibilityCheckerSpec extends ObjectBehavior
{
    function let(RuleCheckerRegistryInterface $registry, EventDispatcher $dispatcher)
    {
        $this->beConstructedWith($registry, $dispatcher);
    }

    function it_is_a_rule_checker()
    {
        $this->shouldBeAnInstanceOf('Sylius\Bundle\PromotionsBundle\Checker\PromotionEligibilityCheckerInterface');
    }

    function it_recognizes_subject_as_eligible_if_all_checkers_recognize_it_as_eligible(
        $registry, RuleCheckerInterface $checker, PromotionSubjectInterface $subject, PromotionInterface $promotion, RuleInterface $rule
    )
    {
        $promotion->getStartsAt()->willReturn(null);
        $promotion->getEndsAt()->willReturn(null);
        $promotion->isCouponBased()->willReturn(false);
        $promotion->getUsageLimit()->willReturn(null);

        $registry->getChecker(RuleInterface::TYPE_ITEM_TOTAL)->willReturn($checker);
        $promotion->getRules()->willReturn(array($rule));
        $rule->getType()->willReturn(RuleInterface::TYPE_ITEM_TOTAL);
        $rule->getConfiguration()->willReturn(array());

        $checker->isEligible($subject, array())->willReturn(true);

        $promotion->hasCoupons()->willReturn(true);
        $subject->getPromotionCoupon()->willReturn(null);

        $this->isEligible($subject, $promotion)->shouldReturn(true);
    }

    function it_recognizes_subject_as_not_eligible_if_any_checker_recognize_it_as_not_eligible(
        $registry, RuleCheckerInterface $checker, PromotionSubjectInterface $subject, PromotionInterface $promotion, RuleInterface $rule
    )
    {
        $promotion->getStartsAt()->willReturn(null);
        $promotion->getEndsAt()->willReturn(null);
        $promotion->isCouponBased()->willReturn(false);
        $promotion->getUsageLimit()->willReturn(null);

        $registry->getChecker(RuleInterface::TYPE_ITEM_TOTAL)->willReturn($checker);
        $promotion->getRules()->willReturn(array($rule));
        $rule->getType()->willReturn(RuleInterface::TYPE_ITEM_TOTAL);
        $rule->getConfiguration()->willReturn(array());

        $checker->isEligible($subject, array())->willReturn(false);

        $this->isEligible($subject, $promotion)->shouldReturn(false);
    }

    function it_recognizes_subject_as_eligible_if_promotion_have_no_coupon_codes(
        PromotionSubjectInterface $subject, PromotionInterface $promotion
    )
    {
        $promotion->getStartsAt()->willReturn(null);
        $promotion->getEndsAt()->willReturn(null);
        $promotion->isCouponBased()->willReturn(false);
        $promotion->getUsageLimit()->willReturn(null);

        $promotion->getRules()->willReturn(array());
        $promotion->hasCoupons()->willReturn(false);

        $this->isEligible($subject, $promotion)->shouldReturn(true);
    }

    function it_recognizes_subject_as_not_eligible_if_coupon_code_does_not_match(
        PromotionSubjectInterface $subject, PromotionInterface $promotion, CouponInterface $coupon
    )
    {
        $promotion->getStartsAt()->willReturn(null);
        $promotion->getEndsAt()->willReturn(null);
        $promotion->isCouponBased()->willReturn(false);
        $promotion->getUsageLimit()->willReturn(null);

        $subject->getPromotionCoupon()->willReturn($coupon);
        $promotion->getRules()->willReturn(array());
        $promotion->hasCoupons()->willReturn(true);
        $promotion->hasCoupon($coupon)->willReturn(false);

        $this->isEligible($subject, $promotion)->shouldReturn(false);
    }

    function it_recognizes_subject_as_eligible_if_coupon_code_match(
        PromotionSubjectInterface $subject, PromotionInterface $promotion, CouponInterface $coupon
    )
    {
        $promotion->getStartsAt()->willReturn(null);
        $promotion->getEndsAt()->willReturn(null);
        $promotion->isCouponBased()->willReturn(false);
        $promotion->getUsageLimit()->willReturn(null);

        $subject->getPromotionCoupon()->willReturn($coupon);
        $promotion->getRules()->willReturn(array());
        $promotion->hasCoupons()->willReturn(true);
        $promotion->hasCoupon($coupon)->willReturn(true);

        $this->isEligible($subject, $promotion)->shouldReturn(true);
    }
}
