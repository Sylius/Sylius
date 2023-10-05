<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ApiBundle\Checker;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Checker\AppliedCouponEligibilityCheckerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionCouponEligibilityCheckerInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface;

final class AppliedCouponEligibilityCheckerSpec extends ObjectBehavior
{
    function let(
        PromotionEligibilityCheckerInterface $promotionChecker,
        PromotionCouponEligibilityCheckerInterface $promotionCouponChecker,
    ): void {
        $this->beConstructedWith($promotionChecker, $promotionCouponChecker);
    }

    function it_implements_promotion_coupon_eligibility_checker_interface(): void
    {
        $this->shouldImplement(AppliedCouponEligibilityCheckerInterface::class);
    }

    function it_returns_false_if_promotion_coupon_is_null(
        PromotionEligibilityCheckerInterface $promotionChecker,
        PromotionCouponEligibilityCheckerInterface $promotionCouponChecker,
        PromotionCouponInterface $promotionCoupon,
        PromotionInterface $promotion,
        OrderInterface $cart,
    ): void {
        $promotionCoupon->getPromotion()->shouldNotBeCalled();
        $promotion->getChannels()->shouldNotBeCalled();
        $promotionChecker->isEligible(Argument::any())->shouldNotBeCalled();
        $promotionCouponChecker->isEligible(Argument::any())->shouldNotBeCalled();

        $this->isEligible(null, $cart)->shouldReturn(false);
    }

    function it_returns_false_if_cart_channel_is_not_one_of_promotion_channels(
        PromotionEligibilityCheckerInterface $promotionChecker,
        PromotionCouponEligibilityCheckerInterface $promotionCouponChecker,
        PromotionCouponInterface $promotionCoupon,
        PromotionInterface $promotion,
        OrderInterface $cart,
        ChannelInterface $firstChannel,
        ChannelInterface $secondChannel,
        ChannelInterface $thirdChannel,
    ): void {
        $promotionCoupon->getPromotion()->willReturn($promotion);

        $promotion->getChannels()->willReturn(new ArrayCollection([
            $secondChannel->getWrappedObject(),
            $thirdChannel->getWrappedObject(),
        ]));
        $cart->getChannel()->willReturn($firstChannel);

        $promotionChecker->isEligible(Argument::any())->shouldNotBeCalled();
        $promotionCouponChecker->isEligible(Argument::any())->shouldNotBeCalled();

        $this->isEligible($promotionCoupon, $cart)->shouldReturn(false);
    }

    function it_returns_false_if_coupon_is_not_eligible(
        PromotionEligibilityCheckerInterface $promotionChecker,
        PromotionCouponEligibilityCheckerInterface $promotionCouponChecker,
        PromotionCouponInterface $promotionCoupon,
        PromotionInterface $promotion,
        OrderInterface $cart,
        ChannelInterface $firstChannel,
        ChannelInterface $secondChannel,
    ): void {
        $promotionCoupon->getPromotion()->willReturn($promotion);

        $promotion->getChannels()->willReturn(new ArrayCollection([
            $firstChannel->getWrappedObject(),
            $secondChannel->getWrappedObject(),
        ]));
        $cart->getChannel()->willReturn($firstChannel);

        $promotionCouponChecker->isEligible($cart, $promotionCoupon)->willReturn(false);
        $promotionChecker->isEligible(Argument::any())->shouldNotBeCalled();

        $this->isEligible($promotionCoupon, $cart)->shouldReturn(false);
    }

    function it_returns_false_if_promotion_is_not_eligible(
        PromotionEligibilityCheckerInterface $promotionChecker,
        PromotionCouponEligibilityCheckerInterface $promotionCouponChecker,
        PromotionCouponInterface $promotionCoupon,
        PromotionInterface $promotion,
        OrderInterface $cart,
        ChannelInterface $firstChannel,
        ChannelInterface $secondChannel,
    ): void {
        $promotionCoupon->getPromotion()->willReturn($promotion);

        $promotion->getChannels()->willReturn(new ArrayCollection([
            $firstChannel->getWrappedObject(),
            $secondChannel->getWrappedObject(),
        ]));
        $cart->getChannel()->willReturn($firstChannel);

        $promotionCouponChecker->isEligible($cart, $promotionCoupon)->willReturn(true);
        $promotionChecker->isEligible($cart, $promotion)->willReturn(false);

        $this->isEligible($promotionCoupon, $cart)->shouldReturn(false);
    }

    function it_returns_true_if_promotion_and_coupon_are_eligible(
        PromotionEligibilityCheckerInterface $promotionChecker,
        PromotionCouponEligibilityCheckerInterface $promotionCouponChecker,
        PromotionCouponInterface $promotionCoupon,
        PromotionInterface $promotion,
        OrderInterface $cart,
        ChannelInterface $firstChannel,
        ChannelInterface $secondChannel,
    ): void {
        $promotionCoupon->getPromotion()->willReturn($promotion);

        $promotion->getChannels()->willReturn(new ArrayCollection([
            $firstChannel->getWrappedObject(),
            $secondChannel->getWrappedObject(),
        ]));
        $cart->getChannel()->willReturn($firstChannel);

        $promotionCouponChecker->isEligible($cart, $promotionCoupon)->willReturn(true);
        $promotionChecker->isEligible($cart, $promotion)->willReturn(true);

        $this->isEligible($promotionCoupon, $cart)->shouldReturn(true);
    }
}
