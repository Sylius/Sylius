<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Promotion\Modifier;

use Sylius\Component\Core\Model\OrderInterface;

/**
 * @author Mateusz Zalewski <mateusz.p.zalewski@gmail.com>
 */
final class OrderPromotionsUsageModifier implements OrderPromotionsUsageModifierInterface
{
    /**
     * {@inheritdoc}
     */
    public function increment(OrderInterface $order)
    {
        foreach ($order->getPromotions() as $promotion) {
            $promotion->incrementUsed();
        }

        $promotionCoupon = $order->getPromotionCoupon();
        if (null !== $promotionCoupon) {
            $promotionCoupon->incrementUsed();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function decrement(OrderInterface $order)
    {
        foreach ($order->getPromotions() as $promotion) {
            $promotion->decrementUsed();
        }

        $promotionCoupon = $order->getPromotionCoupon();
        if (null !== $promotionCoupon) {
            $promotionCoupon->decrementUsed();
        }
    }
}
