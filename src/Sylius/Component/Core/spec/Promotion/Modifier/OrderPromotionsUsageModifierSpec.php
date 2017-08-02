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

namespace spec\Sylius\Component\Core\Promotion\Modifier;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Promotion\Modifier\OrderPromotionsUsageModifier;
use Sylius\Component\Core\Promotion\Modifier\OrderPromotionsUsageModifierInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class OrderPromotionsUsageModifierSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(OrderPromotionsUsageModifier::class);
    }

    function it_implements_an_order_promotions_usage_modifier_interface()
    {
        $this->shouldImplement(OrderPromotionsUsageModifierInterface::class);
    }

    function it_increments_a_usage_of_promotions_applied_on_order(
        OrderInterface $order,
        PromotionInterface $firstPromotion,
        PromotionInterface $secondPromotion
    ) {
        $order->getPromotions()->willReturn([$firstPromotion, $secondPromotion]);
        $order->getPromotionCoupon()->willReturn(null);

        $firstPromotion->incrementUsed()->shouldBeCalled();
        $secondPromotion->incrementUsed()->shouldBeCalled();

        $this->increment($order);
    }

    function it_decrements_a_usage_of_promotions_applied_on_order(
        OrderInterface $order,
        PromotionInterface $firstPromotion,
        PromotionInterface $secondPromotion
    ) {
        $order->getPromotions()->willReturn([$firstPromotion, $secondPromotion]);
        $order->getPromotionCoupon()->willReturn(null);

        $firstPromotion->decrementUsed()->shouldBeCalled();
        $secondPromotion->decrementUsed()->shouldBeCalled();

        $this->decrement($order);
    }

    function it_increments_a_usage_of_promotions_and_promotion_coupon_applied_on_order(
        OrderInterface $order,
        PromotionInterface $firstPromotion,
        PromotionInterface $secondPromotion,
        PromotionCouponInterface $promotionCoupon
    ) {
        $order->getPromotions()->willReturn([$firstPromotion, $secondPromotion]);
        $order->getPromotionCoupon()->willReturn($promotionCoupon);

        $firstPromotion->incrementUsed()->shouldBeCalled();
        $secondPromotion->incrementUsed()->shouldBeCalled();

        $promotionCoupon->incrementUsed()->shouldBeCalled();

        $this->increment($order);
    }

    function it_decrements_a_usage_of_promotions_and_promotion_coupon_applied_on_order(
        OrderInterface $order,
        PromotionInterface $firstPromotion,
        PromotionInterface $secondPromotion,
        PromotionCouponInterface $promotionCoupon
    ) {
        $order->getPromotions()->willReturn([$firstPromotion, $secondPromotion]);
        $order->getPromotionCoupon()->willReturn($promotionCoupon);

        $firstPromotion->decrementUsed()->shouldBeCalled();
        $secondPromotion->decrementUsed()->shouldBeCalled();

        $promotionCoupon->decrementUsed()->shouldBeCalled();

        $this->decrement($order);
    }
}
