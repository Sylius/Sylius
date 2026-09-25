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

namespace Sylius\Component\Core\Promotion\Modifier;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;

final class OrderPromotionsUsageModifier implements OrderPromotionsUsageModifierInterface
{
    public function increment(OrderInterface $order): void
    {
        foreach ($order->getPromotions() as $promotion) {
            if (!$promotion->isTrackUsage()) {
                continue;
            }

            $promotion->incrementUsed();
        }

        $promotionCoupon = $order->getPromotionCoupon();
        if (null !== $promotionCoupon && $promotionCoupon->isTrackUsage()) {
            $promotionCoupon->incrementUsed();
        }
    }

    public function decrement(OrderInterface $order): void
    {
        foreach ($order->getPromotions() as $promotion) {
            if (!$promotion->isTrackUsage()) {
                continue;
            }

            $promotion->decrementUsed();
        }

        /** @var PromotionCouponInterface|null $promotionCoupon */
        $promotionCoupon = $order->getPromotionCoupon();
        if (null === $promotionCoupon) {
            return;
        }

        if (!$promotionCoupon->isTrackUsage()) {
            return;
        }

        if (OrderInterface::STATE_CANCELLED === $order->getState() && !$promotionCoupon->isReusableFromCancelledOrders()) {
            return;
        }

        $promotionCoupon->decrementUsed();
    }
}
