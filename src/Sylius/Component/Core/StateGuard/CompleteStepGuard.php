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
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;

class CompleteStepGuard
{
    public function __construct(
        private StateMachineFactoryInterface $stateMachineFactory,
    ) {
    }

    /**
     * When address is required? It seems like always
     */
    public function address(OrderInterface $order): bool
    {
//        if ($order->isEmpty()) {
//            return false;
//        }

        return true;
    }

    /**
     * When shipping address / method is required? When any item requires shipping
     */
    public function selectShipping(OrderInterface $order): bool
    {
        if ($order->isEmpty()) {
            return false;
        }

        return true;
    }

    /**
     * When payment method is required? When order is not free
     */
    public function selectPayment(OrderInterface $order): bool
    {
        if ($order->isEmpty()) {
            return false;
        }

        return true;
    }

    public function complete(OrderInterface $order): bool
    {
//        $state = $this->stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH);
//        $channel = $order->getChannel();

        // DEBUG
//        var_dump($order->isEmpty());
//        var_dump('shipping required: ' . (int)$order->isShippingRequired());
//        var_dump($order->getShippingAddress() === null);
//        var_dump('payments required: ' . (int)($order->getTotal() > 0));
//        var_dump($order->hasPayments());
//        var_dump($order->getBillingAddress() === null);
//        var_dump($order->getShipments()->count());
//        var_dump($state->getState());
//        var_dump($channel->isSkippingShippingStepAllowed());
//        var_dump($channel->isSkippingPaymentStepAllowed());
        // DEBUG

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
