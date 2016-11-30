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
use Sylius\Component\Promotion\Checker\Eligibility\PromotionCouponEligibilityCheckerInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionCouponUsageLimitEligibilityChecker;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class PromotionCouponUsageLimitEligibilityCheckerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PromotionCouponUsageLimitEligibilityChecker::class);
    }

    function it_is_a_promotion_coupon_eligibility_checker()
    {
        $this->shouldImplement(PromotionCouponEligibilityCheckerInterface::class);
    }

    function it_returns_true_if_usage_limit_is_not_defined(
        PromotionSubjectInterface $promotionSubject,
        PromotionCouponInterface $promotionCoupon
    ) {
        $promotionCoupon->getUsageLimit()->willReturn(null);

        $this->isEligible($promotionSubject, $promotionCoupon)->shouldReturn(true);
    }

    function it_returns_true_if_usage_limit_has_not_been_reached_yet(
        PromotionSubjectInterface $promotionSubject,
        PromotionCouponInterface $promotionCoupon
    ) {
        $promotionCoupon->getUsageLimit()->willReturn(42);
        $promotionCoupon->getUsed()->willReturn(41);

        $this->isEligible($promotionSubject, $promotionCoupon)->shouldReturn(true);
    }

    function it_returns_false_if_usage_limit_has_been_reached(
        PromotionSubjectInterface $promotionSubject,
        PromotionCouponInterface $promotionCoupon
    ) {
        $promotionCoupon->getUsageLimit()->willReturn(42);
        $promotionCoupon->getUsed()->willReturn(42);

        $this->isEligible($promotionSubject, $promotionCoupon)->shouldReturn(false);
    }
}
