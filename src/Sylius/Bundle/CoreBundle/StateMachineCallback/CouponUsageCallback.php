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
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class CouponUsageCallback
{
    /**
     * @param OrderInterface $order
     */
    public function incrementCouponUsage(OrderInterface $order)
    {
        $coupon = $order->getPromotionCoupon();
        if (null !== $coupon) {
            $coupon->incrementUsed();
        }
    }
}
