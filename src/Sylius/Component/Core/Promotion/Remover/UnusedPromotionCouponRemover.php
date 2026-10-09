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

namespace Sylius\Component\Core\Promotion\Remover;

use Sylius\Component\Core\Model\OrderInterface;

final class UnusedPromotionCouponRemover implements UnusedPromotionCouponRemoverInterface
{
    public function remove(OrderInterface $order): void
    {
        $promotionCoupon = $order->getPromotionCoupon();
        if (null === $promotionCoupon) {
            return;
        }

        $promotion = $promotionCoupon->getPromotion();
        if (null !== $promotion && $order->hasPromotion($promotion)) {
            return;
        }

        $order->setPromotionCoupon(null);
    }
}
