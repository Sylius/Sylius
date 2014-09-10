<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\StateMachineCallback;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Promotion\Model\CouponInterface;

/**
 * Increments (or decrements) coupon usage when a coupon is used by an order.
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class CouponUsageCallback
{
    public function incrementCouponUsage(OrderInterface $order)
    {
        foreach ($order->getPromotionCoupons() as $coupon) {
            $coupon->incrementUsed();

            if (CouponInterface::TYPE_GIFT_CARD === $coupon->getType()) {
                $amount = $order->getTotal() < $coupon->getAmount() ? $order->getTotal() : $coupon->getAmount();
                $coupon->setAmount($coupon->getAmount() - $amount);
            }
        }
    }

    public function decrementCouponUsage(OrderInterface $order)
    {
        foreach ($order->getPromotionCoupons() as $coupon) {
            $coupon->decrementUsed();

            if (CouponInterface::TYPE_GIFT_CARD === $coupon->getType()) {
                if (0 === $coupon->getAmount()) {
                    $amount = $order->getTotal();
                } else {
                    $amount = $order->getTotal() + $coupon->getAmount();
                }

                $coupon->setAmount($amount);
            }
        }
    }
}
