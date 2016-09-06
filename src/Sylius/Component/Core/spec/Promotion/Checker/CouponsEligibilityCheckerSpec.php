<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Promotion\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CouponInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Promotion\Checker\CouponsEligibilityChecker;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Promotion\Checker\PromotionSubjectEligibilityCheckerInterface;

/**
 * @mixin CouponsEligibilityChecker
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class CouponsEligibilityCheckerSpec extends ObjectBehavior
{
    function let(OrderRepositoryInterface $orderRepository)
    {
        $this->beConstructedWith($orderRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CouponsEligibilityChecker::class);
    }

    function it_implements_coupons_eligibility_checker_interface()
    {
        $this->shouldImplement(PromotionSubjectEligibilityCheckerInterface::class);
    }

    function it_dispatches_event_and_returns_true_if_subject_coupon_is_eligible_to_promotion_and_coupon_usage_limit_is_0(
        CouponInterface $coupon,
        OrderInterface $subject,
        PromotionInterface $promotion
    ) {
        $subject->getPromotionCoupon()->willReturn($coupon);
        $coupon->getPromotion()->willReturn($promotion);

        $subject->getCustomer()->willReturn(null);
        $coupon->getPerCustomerUsageLimit()->willReturn(0);

        $this->isEligible($subject, $promotion)->shouldReturn(true);
    }

    function it_dispatches_event_and_returns_true_if_subject_coupon_is_eligible_to_promotion_and_number_of_usages_is_lesser_than_coupon_usage_limit(
        OrderRepositoryInterface $orderRepository,
        CouponInterface $coupon,
        CustomerInterface $customer,
        OrderInterface $subject,
        PromotionInterface $promotion
    ) {
        $subject->getPromotionCoupon()->willReturn($coupon);
        $coupon->getPromotion()->willReturn($promotion);

        $subject->getCustomer()->willReturn($customer);
        $coupon->getPerCustomerUsageLimit()->willReturn(5);

        $orderRepository->countByCustomerAndCoupon($customer, $coupon)->willReturn(4);

        $this->isEligible($subject, $promotion)->shouldReturn(true);
    }

    function it_dispatches_event_and_returns_true_if_subject_coupon_is_eligible_to_promotion_and_number_of_usages_is_equal_with_coupon_usage_limit(
        OrderRepositoryInterface $orderRepository,
        CouponInterface $coupon,
        CustomerInterface $customer,
        OrderInterface $subject,
        PromotionInterface $promotion
    ) {
        $subject->getPromotionCoupon()->willReturn($coupon);
        $coupon->getPromotion()->willReturn($promotion);

        $subject->getCustomer()->willReturn($customer);
        $coupon->getPerCustomerUsageLimit()->willReturn(5);

        $orderRepository->countByCustomerAndCoupon($customer, $coupon)->willReturn(5);

        $this->isEligible($subject, $promotion)->shouldReturn(true);
    }

    function it_dispatches_event_and_returns_false_if_coupon_usage_limit_is_greater_than_0_but_subject_has_no_customer(
        CouponInterface $coupon,
        OrderInterface $subject,
        PromotionInterface $promotion
    ) {
        $subject->getPromotionCoupon()->willReturn($coupon);
        $coupon->getPromotion()->willReturn($promotion);

        $subject->getCustomer()->willReturn(null);
        $coupon->getPerCustomerUsageLimit()->willReturn(10);

        $this->isEligible($subject, $promotion)->shouldReturn(false);
    }

    function it_dispatches_event_and_returns_false_if_subject_coupon_is_eligible_to_promotion_and_number_of_usages_is_bigger_than_coupon_usage_limit(
        OrderRepositoryInterface $orderRepository,
        CouponInterface $coupon,
        CustomerInterface $customer,
        OrderInterface $subject,
        PromotionInterface $promotion
    ) {
        $subject->getPromotionCoupon()->willReturn($coupon);
        $coupon->getPromotion()->willReturn($promotion);

        $subject->getCustomer()->willReturn($customer);
        $coupon->getPerCustomerUsageLimit()->willReturn(5);

        $orderRepository->countByCustomerAndCoupon($customer, $coupon)->willReturn(6);

        $this->isEligible($subject, $promotion)->shouldReturn(false);
    }
}
