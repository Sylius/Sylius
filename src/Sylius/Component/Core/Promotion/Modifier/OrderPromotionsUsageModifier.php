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

namespace Sylius\Component\Core\Promotion\Modifier;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;

final class OrderPromotionsUsageModifier implements OrderPromotionsUsageModifierInterface
{
    public function increment(OrderInterface $order): void
    {
        foreach ($order->getPromotions() as $promotion) {
            $promotion->incrementUsed();
        }

        $promotionCoupon = $order->getPromotionCoupon();
        if (null !== $promotionCoupon) {
            $promotionCoupon->incrementUsed();
        }
    }

    public function decrement(OrderInterface $order): void
    {
        foreach ($order->getPromotions() as $promotion) {
            $promotion->decrementUsed();
        }

        /** @var PromotionCouponInterface|null $promotionCoupon */
        $promotionCoupon = $order->getPromotionCoupon();
        if (null === $promotionCoupon) {
            return;
        }

        if (OrderInterface::STATE_CANCELLED === $order->getState() && !$promotionCoupon->isReusableFromCancelledOrders()) {
            return;
        }

        $promotionCoupon->decrementUsed();
    }
}
