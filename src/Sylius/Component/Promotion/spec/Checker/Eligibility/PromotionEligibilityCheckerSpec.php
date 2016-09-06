<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Promotion\Checker\Eligibility;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityChecker;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

/**
 * @mixin PromotionEligibilityChecker
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class PromotionEligibilityCheckerSpec extends ObjectBehavior
{
    function let(
        PromotionEligibilityCheckerInterface $datesEligibilityChecker,
        PromotionEligibilityCheckerInterface $usageLimitEligibilityChecker,
        PromotionEligibilityCheckerInterface $couponsEligibilityChecker,
        PromotionEligibilityCheckerInterface $rulesEligibilityChecker
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
        $this->shouldBeAnInstanceOf(PromotionEligibilityCheckerInterface::class);
    }

    function it_returns_false_if_promotion_is_not_eligible_to_dates(
        PromotionEligibilityCheckerInterface $datesEligibilityChecker,
        PromotionInterface $promotion,
        PromotionSubjectInterface $promotionSubject
    ) {
        $datesEligibilityChecker->isEligible($promotionSubject, $promotion)->willReturn(false);

        $this->isEligible($promotionSubject, $promotion)->shouldReturn(false);
    }

    function it_returns_false_if_promotion_is_not_eligible_to_usage_limit(
        PromotionEligibilityCheckerInterface $datesEligibilityChecker,
        PromotionEligibilityCheckerInterface $usageLimitEligibilityChecker,
        PromotionInterface $promotion,
        PromotionSubjectInterface $promotionSubject
    ) {
        $datesEligibilityChecker->isEligible($promotionSubject, $promotion)->willReturn(true);
        $usageLimitEligibilityChecker->isEligible($promotionSubject, $promotion)->willReturn(false);

        $this->isEligible($promotionSubject, $promotion)->shouldReturn(false);
    }

    function it_returns_false_if_promotion_is_not_eligible_to_rules(
        PromotionEligibilityCheckerInterface $datesEligibilityChecker,
        PromotionEligibilityCheckerInterface $usageLimitEligibilityChecker,
        PromotionEligibilityCheckerInterface $rulesEligibilityChecker,
        PromotionInterface $promotion,
        PromotionSubjectInterface $promotionSubject
    ) {
        $datesEligibilityChecker->isEligible($promotionSubject, $promotion)->willReturn(true);
        $usageLimitEligibilityChecker->isEligible($promotionSubject, $promotion)->willReturn(true);
        $rulesEligibilityChecker->isEligible($promotionSubject, $promotion)->willReturn(false);

        $this->isEligible($promotionSubject, $promotion)->shouldReturn(false);
    }

    function it_returns_true_if_promotion_is_not_coupon_based_and_eligible_to_rules(
        PromotionEligibilityCheckerInterface $datesEligibilityChecker,
        PromotionEligibilityCheckerInterface $usageLimitEligibilityChecker,
        PromotionEligibilityCheckerInterface $rulesEligibilityChecker,
        PromotionInterface $promotion,
        PromotionSubjectInterface $promotionSubject
    ) {
        $datesEligibilityChecker->isEligible($promotionSubject, $promotion)->willReturn(true);
        $usageLimitEligibilityChecker->isEligible($promotionSubject, $promotion)->willReturn(true);
        $rulesEligibilityChecker->isEligible($promotionSubject, $promotion)->willReturn(true);

        $promotion->isCouponBased()->willReturn(false);

        $this->isEligible($promotionSubject, $promotion)->shouldReturn(true);
    }

    function it_returns_false_if_promotion_is_coupon_based_and_eligible_to_rules_but_not_eligible_to_coupons(
        PromotionEligibilityCheckerInterface $datesEligibilityChecker,
        PromotionEligibilityCheckerInterface $usageLimitEligibilityChecker,
        PromotionEligibilityCheckerInterface $couponsEligibilityChecker,
        PromotionEligibilityCheckerInterface $rulesEligibilityChecker,
        PromotionInterface $promotion,
        PromotionSubjectInterface $promotionSubject
    ) {
        $datesEligibilityChecker->isEligible($promotionSubject, $promotion)->willReturn(true);
        $usageLimitEligibilityChecker->isEligible($promotionSubject, $promotion)->willReturn(true);
        $rulesEligibilityChecker->isEligible($promotionSubject, $promotion)->willReturn(true);

        $promotion->isCouponBased()->willReturn(true);

        $couponsEligibilityChecker->isEligible($promotionSubject, $promotion)->willReturn(false);

        $this->isEligible($promotionSubject, $promotion)->shouldReturn(false);
    }

    function it_returns_true_if_promotion_is_coupon_based_and_eligible_to_rules_and_eligible_to_coupons(
        PromotionEligibilityCheckerInterface $datesEligibilityChecker,
        PromotionEligibilityCheckerInterface $usageLimitEligibilityChecker,
        PromotionEligibilityCheckerInterface $couponsEligibilityChecker,
        PromotionEligibilityCheckerInterface $rulesEligibilityChecker,
        PromotionInterface $promotion,
        PromotionSubjectInterface $promotionSubject
    ) {
        $datesEligibilityChecker->isEligible($promotionSubject, $promotion)->willReturn(true);
        $usageLimitEligibilityChecker->isEligible($promotionSubject, $promotion)->willReturn(true);
        $rulesEligibilityChecker->isEligible($promotionSubject, $promotion)->willReturn(true);

        $promotion->isCouponBased()->willReturn(true);

        $couponsEligibilityChecker->isEligible($promotionSubject, $promotion)->willReturn(true);

        $this->isEligible($promotionSubject, $promotion)->shouldReturn(true);
    }
}
