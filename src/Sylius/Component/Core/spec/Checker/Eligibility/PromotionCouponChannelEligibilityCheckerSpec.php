<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Core\Checker\Eligibility;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionCouponEligibilityCheckerInterface;
use Sylius\Component\Promotion\Model\Promotion;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

final class PromotionCouponChannelEligibilityCheckerSpec extends ObjectBehavior
{
    function it_is_a_promotion_coupon_eligibility_checker(): void
    {
        $this->shouldImplement(PromotionCouponEligibilityCheckerInterface::class);
    }

    function it_returns_true_if_promotion_coupon_is_enabled_in_channel(
        OrderInterface $promotionSubject,
        PromotionCouponInterface $promotionCoupon,
        ChannelInterface $channel,
        PromotionInterface $promotion,
    ): void {
        $promotionSubject->getChannel()->willReturn($channel);
        $promotionCoupon->getPromotion()->willReturn($promotion);
        $promotion->hasChannel($channel)->willReturn(true);

        $this->isEligible($promotionSubject, $promotionCoupon)->shouldReturn(true);
    }

    function it_returns_false_if_promotion_coupon_is_not_enabled_in_channel(
        OrderInterface $promotionSubject,
        PromotionCouponInterface $promotionCoupon,
        ChannelInterface $channel,
        PromotionInterface $promotion,
    ): void {
        $promotionSubject->getChannel()->willReturn($channel);
        $promotionCoupon->getPromotion()->willReturn($promotion);
        $promotion->hasChannel($channel)->willReturn(false);

        $this->isEligible($promotionSubject, $promotionCoupon)->shouldReturn(false);
    }

    function it_throws_invalid_argument_exception_when_wrong_promotion_subject_provided(
        PromotionSubjectInterface $promotionSubject,
        PromotionCouponInterface $promotionCoupon,
    ): void {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('isEligible', [$promotionSubject, $promotionCoupon])
        ;
    }

    function it_throws_invalid_argument_exception_when_different_parameter_than_promotion_interface_provided(
        OrderInterface $promotionSubject,
        PromotionCouponInterface $promotionCoupon,
        ChannelInterface $channel,
        Promotion $promotion,
    ): void {
        $promotionSubject->getChannel()->willReturn($channel);
        $promotionCoupon->getPromotion()->willReturn($promotion);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('isEligible', [$promotionSubject, $promotionCoupon])
        ;
    }

    function it_throws_invalid_argument_exception_when_order_channel_is_null(
        OrderInterface $promotionSubject,
        PromotionCouponInterface $promotionCoupon,
        PromotionInterface $promotion,
    ): void {
        $promotionSubject->getChannel()->willReturn(null);
        $promotionCoupon->getPromotion()->willReturn($promotion);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('isEligible', [$promotionSubject, $promotionCoupon])
        ;
    }
}
