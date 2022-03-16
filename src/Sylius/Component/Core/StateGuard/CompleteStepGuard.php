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

namespace Sylius\Component\Core\StateGuard;

use Sylius\Component\Core\Model\OrderInterface;

class CompleteStepGuard
{
    /**
     * You can skip shipping step and payment step when order is free AND not require shipping.
     */
    public function isSatisfiedBy(OrderInterface $order): bool
    {
//        IDEA - Requests from API will be triggered from step after addressing, therefore
//        var_dump($order->isShippingRequired());
//        var_dump($order->getShippingAddress() === null);
//        var_dump($order->getTotal());
//        var_dump($order->hasPayments());
//        var_dump($order->getBillingAddress() === null);
//        var_dump($order->getShipments()->count());



        if ($order->isEmpty()) {
            return false;
        }

        if ($order->isShippingRequired() === true) {
            if ($order->getShippingAddress() === null) {
                return false;
            }
        }

        if ($order->getTotal() > 0) {
            if ($order->hasPayments() === false) {
                return false;
            }
        }

        if ($order->getBillingAddress() === null) {
            return false;
        }

        return $order->getChannel()->isSkippingPaymentStepAllowed() && $order->getChannel()->isSkippingPaymentStepAllowed();
    }
}
