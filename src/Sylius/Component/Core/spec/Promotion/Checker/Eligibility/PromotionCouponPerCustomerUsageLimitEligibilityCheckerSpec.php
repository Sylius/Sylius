<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Promotion\Checker\Eligibility;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface as CorePromotionCouponInterface;
use Sylius\Component\Core\Promotion\Checker\Eligibility\PromotionCouponPerCustomerUsageLimitEligibilityChecker;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionCouponEligibilityCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class PromotionCouponPerCustomerUsageLimitEligibilityCheckerSpec extends ObjectBehavior
{
    function let(OrderRepositoryInterface $orderRepository)
    {
        $this->beConstructedWith($orderRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PromotionCouponPerCustomerUsageLimitEligibilityChecker::class);
    }

    function it_implements_a_promotion_coupon_eligibility_checker_interface()
    {
        $this->shouldImplement(PromotionCouponEligibilityCheckerInterface::class);
    }

    function it_returns_false_if_promotion_coupon_has_reached_its_per_customer_usage_limit(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $promotionSubject,
        CorePromotionCouponInterface $promotionCoupon,
        CustomerInterface $customer
    ) {
        $customer->getId()->willReturn(1);
        $promotionSubject->getCustomer()->willReturn($customer);
        $promotionCoupon->getPerCustomerUsageLimit()->willReturn(42);

        $orderRepository->countByCustomerAndCoupon($customer, $promotionCoupon)->willReturn(42);

        $this->isEligible($promotionSubject, $promotionCoupon)->shouldReturn(false);
    }

    function it_returns_true_if_promotion_coupon_has_not_reached_its_per_customer_usage_limit(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $promotionSubject,
        CorePromotionCouponInterface $promotionCoupon,
        CustomerInterface $customer
    ) {
        $customer->getId()->willReturn(1);
        $promotionSubject->getCustomer()->willReturn($customer);
        $promotionCoupon->getPerCustomerUsageLimit()->willReturn(42);

        $orderRepository->countByCustomerAndCoupon($customer, $promotionCoupon)->willReturn(41);

        $this->isEligible($promotionSubject, $promotionCoupon)->shouldReturn(true);
    }

    function it_returns_true_if_promotion_subject_has_customer_that_is_not_persisted(
        OrderInterface $promotionSubject,
        CorePromotionCouponInterface $promotionCoupon,
        CustomerInterface $customer
    ) {
        $customer->getId()->willReturn(null);
        $promotionSubject->getCustomer()->willReturn($customer);
        $promotionCoupon->getPerCustomerUsageLimit()->willReturn(42);

        $this->isEligible($promotionSubject, $promotionCoupon)->shouldReturn(true);
    }

    function it_returns_true_if_promotion_subject_has_no_customer(
        OrderInterface $promotionSubject,
        CorePromotionCouponInterface $promotionCoupon
    ) {
        $promotionSubject->getCustomer()->willReturn(null);
        $promotionCoupon->getPerCustomerUsageLimit()->willReturn(42);

        $this->isEligible($promotionSubject, $promotionCoupon)->shouldReturn(true);
    }

    function it_returns_true_if_promotion_coupon_has_no_per_customer_usage_limit(
        OrderInterface $promotionSubject,
        CorePromotionCouponInterface $promotionCoupon
    ) {
        $promotionCoupon->getPerCustomerUsageLimit()->willReturn(null);

        $this->isEligible($promotionSubject, $promotionCoupon)->shouldReturn(true);
    }

    function it_returns_true_if_promotion_coupon_is_not_a_core_one(
        OrderInterface $promotionSubject,
        PromotionCouponInterface $promotionCoupon
    ) {
        $this->isEligible($promotionSubject, $promotionCoupon)->shouldReturn(true);
    }

    function it_returns_true_if_promotion_subject_is_not_a_core_order(
        PromotionSubjectInterface $promotionSubject,
        CorePromotionCouponInterface $promotionCoupon
    ) {
        $this->isEligible($promotionSubject, $promotionCoupon)->shouldReturn(true);
    }
}
