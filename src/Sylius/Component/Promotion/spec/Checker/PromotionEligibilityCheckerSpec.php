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
use Sylius\Component\Promotion\Checker\PromotionEligibilityChecker;
use Sylius\Component\Promotion\Checker\PromotionEligibilityCheckerInterface;
use Sylius\Component\Promotion\Checker\PromotionSubjectEligibilityCheckerInterface;
use Sylius\Component\Promotion\Checker\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\CouponInterface;
use Sylius\Component\Promotion\Model\PromotionCouponAwareSubjectInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Promotion\Model\RuleInterface;
use Sylius\Component\Promotion\SyliusPromotionEvents;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @mixin PromotionEligibilityChecker
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class PromotionEligibilityCheckerSpec extends ObjectBehavior
{
    function let(
        PromotionEligibilityCheckerInterface $datesEligibilityChecker,
        PromotionEligibilityCheckerInterface $usageLimitEligibilityChecker,
        PromotionSubjectEligibilityCheckerInterface $couponsEligibilityChecker,
        PromotionSubjectEligibilityCheckerInterface $rulesEligibilityChecker
    ) {
        $this->beConstructedWith(
            $datesEligibilityChecker,
            $usageLimitEligibilityChecker,
            $couponsEligibilityChecker,
            $rulesEligibilityChecker
        );
    }

    function it_is_a_rule_checker()
    {
        $this->shouldBeAnInstanceOf(PromotionSubjectEligibilityCheckerInterface::class);
    }

    function it_returns_false_if_promotion_is_not_eligible_to_dates(
        PromotionEligibilityCheckerInterface $datesEligibilityChecker,
        PromotionInterface $promotion,
        PromotionSubjectInterface $subject
    ) {
        $datesEligibilityChecker->isEligible($promotion)->willReturn(false);

        $this->isEligible($subject, $promotion)->shouldReturn(false);
    }

    function it_returns_false_if_promotion_is_not_eligible_to_usage_limit(
        PromotionEligibilityCheckerInterface $datesEligibilityChecker,
        PromotionEligibilityCheckerInterface $usageLimitEligibilityChecker,
        PromotionInterface $promotion,
        PromotionSubjectInterface $subject
    ) {
        $datesEligibilityChecker->isEligible($promotion)->willReturn(true);
        $usageLimitEligibilityChecker->isEligible($promotion)->willReturn(false);

        $this->isEligible($subject, $promotion)->shouldReturn(false);
    }

    function it_returns_false_if_promotion_is_not_eligible_to_rules(
        PromotionEligibilityCheckerInterface $datesEligibilityChecker,
        PromotionEligibilityCheckerInterface $usageLimitEligibilityChecker,
        PromotionSubjectEligibilityCheckerInterface $rulesEligibilityChecker,
        PromotionInterface $promotion,
        PromotionSubjectInterface $subject
    ) {
        $datesEligibilityChecker->isEligible($promotion)->willReturn(true);
        $usageLimitEligibilityChecker->isEligible($promotion)->willReturn(true);
        $rulesEligibilityChecker->isEligible($subject, $promotion)->willReturn(false);

        $this->isEligible($subject, $promotion)->shouldReturn(false);
    }

    function it_returns_true_if_promotion_is_not_coupon_based_and_eligible_to_rules(
        PromotionEligibilityCheckerInterface $datesEligibilityChecker,
        PromotionEligibilityCheckerInterface $usageLimitEligibilityChecker,
        PromotionSubjectEligibilityCheckerInterface $rulesEligibilityChecker,
        PromotionInterface $promotion,
        PromotionSubjectInterface $subject
    ) {
        $datesEligibilityChecker->isEligible($promotion)->willReturn(true);
        $usageLimitEligibilityChecker->isEligible($promotion)->willReturn(true);
        $rulesEligibilityChecker->isEligible($subject, $promotion)->willReturn(true);

        $promotion->isCouponBased()->willReturn(false);

        $this->isEligible($subject, $promotion)->shouldReturn(true);
    }

    function it_returns_false_if_promotion_is_coupon_based_and_eligible_to_rules_but_not_eligible_to_coupons(
        PromotionEligibilityCheckerInterface $datesEligibilityChecker,
        PromotionEligibilityCheckerInterface $usageLimitEligibilityChecker,
        PromotionSubjectEligibilityCheckerInterface $couponsEligibilityChecker,
        PromotionSubjectEligibilityCheckerInterface $rulesEligibilityChecker,
        PromotionInterface $promotion,
        PromotionSubjectInterface $subject
    ) {
        $datesEligibilityChecker->isEligible($promotion)->willReturn(true);
        $usageLimitEligibilityChecker->isEligible($promotion)->willReturn(true);
        $rulesEligibilityChecker->isEligible($subject, $promotion)->willReturn(true);

        $promotion->isCouponBased()->willReturn(true);

        $couponsEligibilityChecker->isEligible($subject, $promotion)->willReturn(false);

        $this->isEligible($subject, $promotion)->shouldReturn(false);
    }

    function it_returns_true_if_promotion_is_coupon_based_and_eligible_to_rules_and_eligible_to_coupons(
        PromotionEligibilityCheckerInterface $datesEligibilityChecker,
        PromotionEligibilityCheckerInterface $usageLimitEligibilityChecker,
        PromotionSubjectEligibilityCheckerInterface $couponsEligibilityChecker,
        PromotionSubjectEligibilityCheckerInterface $rulesEligibilityChecker,
        PromotionInterface $promotion,
        PromotionSubjectInterface $subject
    ) {
        $datesEligibilityChecker->isEligible($promotion)->willReturn(true);
        $usageLimitEligibilityChecker->isEligible($promotion)->willReturn(true);
        $rulesEligibilityChecker->isEligible($subject, $promotion)->willReturn(true);

        $promotion->isCouponBased()->willReturn(true);

        $couponsEligibilityChecker->isEligible($subject, $promotion)->willReturn(true);

        $this->isEligible($subject, $promotion)->shouldReturn(true);
    }
}
