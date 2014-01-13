<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Increments coupon usage when a coupon is used by an order
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class CouponUsageListener
{
    public function handleCouponUsage(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new \InvalidArgumentException('Coupon usage listener requires event subject to be instance of "Sylius\Bundle\CoreBundle\Model\OrderInterface"');
        }

        if ($coupon = $order->getPromotionCoupon()) {
            $coupon->incrementUsed();
        }
    }
}
