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

/**
 * Increments coupon usage when a coupon is used by an order
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class CouponUsageCallback
{
    public function incrementCouponUsage(OrderInterface $order)
    {
        foreach ($order->getPromotionCoupons() as $coupon) {
            $coupon->incrementUsed();
        }
    }
}
