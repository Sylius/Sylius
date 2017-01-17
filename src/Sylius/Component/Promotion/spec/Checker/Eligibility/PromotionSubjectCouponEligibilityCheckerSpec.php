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
use Sylius\Component\Promotion\Checker\Eligibility\PromotionSubjectCouponEligibilityChecker;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Model\PromotionCouponAwarePromotionSubjectInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class PromotionSubjectCouponEligibilityCheckerSpec extends ObjectBehavior
{
    function let(PromotionCouponEligibilityCheckerInterface $promotionCouponEligibilityChecker)
    {
        $this->beConstructedWith($promotionCouponEligibilityChecker);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PromotionSubjectCouponEligibilityChecker::class);
    }

    function it_implements_a_promotion_eligibility_checker_interface()
    {
        $this->shouldImplement(PromotionEligibilityCheckerInterface::class);
    }

    function it_returns_true_if_subject_coupons_are_eligible_to_promotion(
        PromotionCouponEligibilityCheckerInterface $promotionCouponEligibilityChecker,
        PromotionCouponAwarePromotionSubjectInterface $promotionSubject,
        PromotionInterface $promotion,
        PromotionCouponInterface $promotionCoupon
    ) {
        $promotion->isCouponBased()->willReturn(true);

        $promotionSubject->getPromotionCoupon()->willReturn($promotionCoupon);
        $promotionCoupon->getPromotion()->willReturn($promotion);

        $promotionCouponEligibilityChecker->isEligible($promotionSubject, $promotionCoupon)->willReturn(true);

        $this->isEligible($promotionSubject, $promotion)->shouldReturn(true);
    }

    function it_returns_false_if_subject_is_not_coupon_aware(
        PromotionSubjectInterface $promotionSubject,
        PromotionInterface $promotion
    ) {
        $promotion->isCouponBased()->willReturn(true);

        $this->isEligible($promotionSubject, $promotion)->shouldReturn(false);
    }

    function it_returns_false_if_subject_has_no_coupon(
        PromotionCouponAwarePromotionSubjectInterface $promotionSubject,
        PromotionInterface $promotion
    ) {
        $promotion->isCouponBased()->willReturn(true);

        $promotionSubject->getPromotionCoupon()->willReturn(null);

        $this->isEligible($promotionSubject, $promotion)->shouldReturn(false);
    }

    function it_returns_false_if_subject_coupons_comes_from_an_another_promotion(
        PromotionCouponAwarePromotionSubjectInterface $promotionSubject,
        PromotionInterface $promotion,
        PromotionInterface $otherPromotion,
        PromotionCouponInterface $promotionCoupon
    ) {
        $promotion->isCouponBased()->willReturn(true);

        $promotionSubject->getPromotionCoupon()->willReturn($promotionCoupon);
        $promotionCoupon->getPromotion()->willReturn($otherPromotion);

        $this->isEligible($promotionSubject, $promotion)->shouldReturn(false);
    }

    function it_returns_false_if_subject_coupons_is_not_eligible(
        PromotionCouponEligibilityCheckerInterface $promotionCouponEligibilityChecker,
        PromotionCouponAwarePromotionSubjectInterface $promotionSubject,
        PromotionInterface $promotion,
        PromotionCouponInterface $promotionCoupon
    ) {
        $promotion->isCouponBased()->willReturn(true);

        $promotionSubject->getPromotionCoupon()->willReturn($promotionCoupon);
        $promotionCoupon->getPromotion()->willReturn($promotion);

        $promotionCouponEligibilityChecker->isEligible($promotionSubject, $promotionCoupon)->willReturn(false);

        $this->isEligible($promotionSubject, $promotion)->shouldReturn(false);
    }
}
