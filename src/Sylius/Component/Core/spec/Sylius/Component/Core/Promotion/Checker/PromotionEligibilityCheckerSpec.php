<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Promotion\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CouponInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\UserInterface;
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

    public function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Promotion\Checker\PromotionEligibilityChecker');
    }

    public function it_recognizes_subject_as_not_eligible_if_coupon_per_user_limit_reached(
        $subjectRepository, OrderInterface $subject, PromotionInterface $promotion, UserInterface $user, CouponInterface $coupon
    )
    {
        $subject->getUser()->willReturn($user);

        $coupon->getCode()->willReturn('D0003');
        $coupon->getPerUserUsageLimit()->willReturn(1);
        $coupon->getPromotion()->willReturn($promotion);

        $subject->getPromotionCoupon()->willReturn($coupon);

        $promotion->getRules()->willReturn(array());
        $promotion->getStartsAt()->willReturn(null);
        $promotion->getEndsAt()->willReturn(null);
        $promotion->isCouponBased()->willReturn(true);
        $promotion->hasCoupons()->willReturn(true);
        $promotion->hasCoupon($coupon)->willReturn(true);
        $promotion->getUsageLimit()->willReturn(null);
        $promotion->getCoupons()->willReturn(array($coupon));

        $subjectRepository->countByUserAndCoupon($user, $coupon)->willReturn(2);

        $this->isEligible($subject, $promotion)->shouldReturn(false);
    }

    public function it_recognizes_subject_as_eligible_if_coupon_per_user_limit_not_reached(
        $subjectRepository, OrderInterface $subject, PromotionInterface $promotion, UserInterface $user, CouponInterface $coupon
    )
    {
        $subject->getUser()->willReturn($user);

        $coupon->getCode()->willReturn('D0003');
        $coupon->getPerUserUsageLimit()->willReturn(1);
        $coupon->getPromotion()->willReturn($promotion);

        $subject->getPromotionCoupon()->willReturn($coupon);

        $promotion->getRules()->willReturn(array());
        $promotion->getStartsAt()->willReturn(null);
        $promotion->getEndsAt()->willReturn(null);
        $promotion->isCouponBased()->willReturn(true);
        $promotion->hasCoupons()->willReturn(true);
        $promotion->hasCoupon($coupon)->willReturn(true);
        $promotion->getUsageLimit()->willReturn(null);
        $promotion->getCoupons()->willReturn(array($coupon));

        $subjectRepository->countByUserAndCoupon($user, $coupon)->willReturn(0);

        $this->isEligible($subject, $promotion)->shouldReturn(true);
    }

    public function it_recognizes_subject_as_not_eligible_if_user_not_linked_to_order(
        OrderInterface $subject, PromotionInterface $promotion, CouponInterface $coupon
    )
    {
        $subject->getUser()->willReturn(null);

        $coupon->getCode()->willReturn('D0003');
        $coupon->getPerUserUsageLimit()->willReturn(1);
        $coupon->getPromotion()->willReturn($promotion);

        $subject->getPromotionCoupon()->willReturn($coupon);

        $promotion->getRules()->willReturn(array());
        $promotion->getStartsAt()->willReturn(null);
        $promotion->getEndsAt()->willReturn(null);
        $promotion->isCouponBased()->willReturn(true);
        $promotion->hasCoupons()->willReturn(true);
        $promotion->hasCoupon($coupon)->willReturn(true);
        $promotion->getUsageLimit()->willReturn(null);
        $promotion->getCoupons()->willReturn(array($coupon));

        $this->isEligible($subject, $promotion)->shouldReturn(false);
    }
}
