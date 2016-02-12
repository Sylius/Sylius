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
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Myke Hines <myke@webhines.com>
 */
class PromotionEligibilityCheckerSpec extends ObjectBehavior
{
    public function let(OrderRepositoryInterface $subjectRepository, ServiceRegistryInterface $registry, EventDispatcherInterface $dispatcher)
    {
        $this->beConstructedWith($subjectRepository, $registry, $dispatcher);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Promotion\Checker\PromotionEligibilityChecker');
    }

    public function it_recognizes_subject_as_not_eligible_if_coupon_per_customer_limit_reached(
        $subjectRepository, OrderInterface $subject, PromotionInterface $promotion, CustomerInterface $customer, CouponInterface $coupon
    ) {
        $subject->getCustomer()->willReturn($customer);

        $coupon->getCode()->willReturn('D0003');
        $coupon->getPerCustomerUsageLimit()->willReturn(1);
        $coupon->getPromotion()->willReturn($promotion);

        $subject->getPromotionCoupon()->willReturn($coupon);

        $promotion->hasRules()->willReturn(false);
        $promotion->getStartsAt()->willReturn(null);
        $promotion->getEndsAt()->willReturn(null);
        $promotion->isCouponBased()->willReturn(true);
        $promotion->hasCoupons()->willReturn(true);
        $promotion->hasCoupon($coupon)->willReturn(true);
        $promotion->getUsageLimit()->willReturn(null);
        $promotion->getCoupons()->willReturn([$coupon]);

        $subjectRepository->countByCustomerAndCoupon($customer, $coupon)->willReturn(2);

        $this->isEligible($subject, $promotion)->shouldReturn(false);
    }

    public function it_recognizes_subject_as_eligible_if_coupon_per_customer_limit_not_reached(
        $subjectRepository, OrderInterface $subject, PromotionInterface $promotion, CustomerInterface $customer, CouponInterface $coupon
    ) {
        $subject->getCustomer()->willReturn($customer);

        $coupon->getCode()->willReturn('D0003');
        $coupon->getPerCustomerUsageLimit()->willReturn(1);
        $coupon->getPromotion()->willReturn($promotion);

        $subject->getPromotionCoupon()->willReturn($coupon);

        $promotion->hasRules()->willReturn(false);
        $promotion->getStartsAt()->willReturn(null);
        $promotion->getEndsAt()->willReturn(null);
        $promotion->isCouponBased()->willReturn(true);
        $promotion->hasCoupons()->willReturn(true);
        $promotion->hasCoupon($coupon)->willReturn(true);
        $promotion->getUsageLimit()->willReturn(null);
        $promotion->getCoupons()->willReturn([$coupon]);

        $subjectRepository->countByCustomerAndCoupon($customer, $coupon)->willReturn(0);

        $this->isEligible($subject, $promotion)->shouldReturn(true);
    }

    public function it_recognizes_subject_as_not_eligible_if_customer_not_linked_to_order_and_coupon_restricted_by_customer(
        OrderInterface $subject, PromotionInterface $promotion, CouponInterface $coupon
    ) {
        $subject->getCustomer()->willReturn(null);

        $coupon->getCode()->willReturn('D0003');
        $coupon->getPerCustomerUsageLimit()->willReturn(1);
        $coupon->getPromotion()->willReturn($promotion);

        $subject->getPromotionCoupon()->willReturn($coupon);

        $promotion->hasRules()->willReturn(false);
        $promotion->getStartsAt()->willReturn(null);
        $promotion->getEndsAt()->willReturn(null);
        $promotion->isCouponBased()->willReturn(true);
        $promotion->hasCoupons()->willReturn(true);
        $promotion->hasCoupon($coupon)->willReturn(true);
        $promotion->getUsageLimit()->willReturn(null);
        $promotion->getCoupons()->willReturn([$coupon]);

        $this->isEligible($subject, $promotion)->shouldReturn(false);
    }

    public function it_recognizes_subject_as_eligible_if_customer_not_linked_to_order_and_coupon_not_restricted_by_customer(
        OrderInterface $subject, PromotionInterface $promotion, CouponInterface $coupon
    ) {
        $subject->getCustomer()->willReturn(null);

        $coupon->getCode()->willReturn('D0003');
        $coupon->getPerCustomerUsageLimit()->willReturn(0);
        $coupon->getPromotion()->willReturn($promotion);

        $subject->getPromotionCoupon()->willReturn($coupon);

        $promotion->hasRules()->willReturn(false);
        $promotion->getStartsAt()->willReturn(null);
        $promotion->getEndsAt()->willReturn(null);
        $promotion->isCouponBased()->willReturn(true);
        $promotion->hasCoupons()->willReturn(true);
        $promotion->hasCoupon($coupon)->willReturn(true);
        $promotion->getUsageLimit()->willReturn(null);
        $promotion->getCoupons()->willReturn([$coupon]);

        $this->isEligible($subject, $promotion)->shouldReturn(true);
    }
}
