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
use Sylius\Component\Promotion\Checker\PromotionSubjectEligibilityCheckerInterface;
use Sylius\Component\Promotion\Model\CouponInterface;
use Sylius\Component\Promotion\Model\PromotionCouponAwareSubjectInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Promotion\SyliusPromotionEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class CouponsEligibilityCheckerSpec extends ObjectBehavior
{
    function let(EventDispatcherInterface $eventDispatcher)
    {
        $this->beConstructedWith($eventDispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Promotion\Checker\CouponsEligibilityChecker');
    }

    function it_implements_coupons_eligibility_checker_interface()
    {
        $this->shouldImplement(PromotionSubjectEligibilityCheckerInterface::class);
    }

    function it_dispatches_event_and_returns_true_if_subject_coupons_are_eligible_to_promotion(
        $eventDispatcher,
        CouponInterface $coupon,
        PromotionInterface $promotion,
        PromotionCouponAwareSubjectInterface $subject
    ) {
        $subject->getPromotionCoupon()->willReturn($coupon);
        $coupon->getPromotion()->willReturn($promotion);

        $eventDispatcher
            ->dispatch(SyliusPromotionEvents::COUPON_ELIGIBLE, Argument::type(GenericEvent::class))
            ->shouldBeCalled()
        ;

        $this->isEligible($subject, $promotion)->shouldReturn(true);
    }

    function it_returns_false_if_subject_coupons_are_not_eligible_to_promotion(
        $eventDispatcher,
        CouponInterface $coupon,
        PromotionInterface $promotion,
        PromotionInterface $otherPromotion,
        PromotionCouponAwareSubjectInterface $subject
    ) {
        $subject->getPromotionCoupon()->willReturn($coupon);
        $coupon->getPromotion()->willReturn($otherPromotion);

        $eventDispatcher
            ->dispatch(SyliusPromotionEvents::COUPON_NOT_ELIGIBLE, Argument::type(GenericEvent::class))
            ->shouldBeCalled()
        ;

        $this->isEligible($subject, $promotion)->shouldReturn(false);
    }

    function it_returns_false_if_subject_has_no_coupon(
        $eventDispatcher,
        PromotionInterface $promotion,
        PromotionCouponAwareSubjectInterface $subject
    ) {
        $subject->getPromotionCoupon()->willReturn(null);

        $eventDispatcher
            ->dispatch(SyliusPromotionEvents::COUPON_NOT_ELIGIBLE, Argument::type(GenericEvent::class))
            ->shouldNotBeCalled()
        ;

        $this->isEligible($subject, $promotion)->shouldReturn(false);
    }

    function it_returns_false_if_subject_is_not_coupon_aware(
        PromotionInterface $promotion,
        PromotionSubjectInterface $subject
    ) {
        $this->isEligible($subject, $promotion)->shouldReturn(false);
    }
}
