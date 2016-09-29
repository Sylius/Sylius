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
use Sylius\Component\Promotion\Checker\Eligibility\CouponsEligibilityChecker;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Model\PromotionCouponAwarePromotionSubjectInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

/**
 * @mixin CouponsEligibilityChecker
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class CouponsEligibilityCheckerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(CouponsEligibilityChecker::class);
    }

    function it_implements_coupons_eligibility_checker_interface()
    {
        $this->shouldImplement(PromotionEligibilityCheckerInterface::class);
    }

    function it_dispatches_event_and_returns_true_if_subject_coupons_are_eligible_to_promotion(
        PromotionCouponAwarePromotionSubjectInterface $subject,
        PromotionInterface $promotion,
        PromotionCouponInterface $coupon
    ) {
        $subject->getPromotionCoupon()->willReturn($coupon);
        $promotion->isCouponBased()->willReturn(true);

        $coupon->getPromotion()->willReturn($promotion);

        $this->isEligible($subject, $promotion)->shouldReturn(true);
    }

    function it_returns_false_if_subject_coupons_are_not_eligible_to_promotion(
        PromotionCouponAwarePromotionSubjectInterface $subject,
        PromotionInterface $promotion,
        PromotionInterface $otherPromotion,
        PromotionCouponInterface $coupon
    ) {
        $subject->getPromotionCoupon()->willReturn($coupon);
        $promotion->isCouponBased()->willReturn(true);

        $coupon->getPromotion()->willReturn($otherPromotion);

        $this->isEligible($subject, $promotion)->shouldReturn(false);
    }

    function it_returns_false_if_subject_has_no_coupon(
        PromotionCouponAwarePromotionSubjectInterface $subject,
        PromotionInterface $promotion
    ) {
        $subject->getPromotionCoupon()->willReturn(null);
        $promotion->isCouponBased()->willReturn(true);

        $this->isEligible($subject, $promotion)->shouldReturn(false);
    }

    function it_returns_false_if_subject_is_not_coupon_aware(
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion
    ) {
        $promotion->isCouponBased()->willReturn(true);

        $this->isEligible($subject, $promotion)->shouldReturn(false);
    }
}
