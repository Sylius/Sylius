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
     * When address is required? It seems like always
     * But should we in any instance block the state change? No
     */
    public function address(OrderInterface $order): bool
    {
        return false === $order->isEmpty();
    }

    /**
     * When shipping address / method is required? When any item requires shipping
     * But should we in any instance block the state change? No
     */
    public function selectShipping(OrderInterface $order): bool
    {
        return false === $order->isEmpty();
    }

    /**
     * When payment method is required? When order is not free
     * But should we in any instance block the state change? No
     */
    public function selectPayment(OrderInterface $order): bool
    {
        return false === $order->isEmpty();
    }

    public function complete(OrderInterface $order): bool
    {
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

        return true;
    }
}
