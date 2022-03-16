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
use Sylius\Component\Core\OrderCheckoutTransitions;

class CompleteStepGuard
{
    public function __construct(
        private StateMachineFactoryInterface $stateMachineFactory,
    ) {
    }

    /**
     * You can skip shipping step and payment step when order is free AND not require shipping.
     */
    public function isSatisfiedBy(OrderInterface $order): bool
    {
        $state = $this->stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH);
        $channel = $order->getChannel();

        // DEBUG
        var_dump($order->isEmpty());
        var_dump($order->isShippingRequired());
        var_dump($order->getShippingAddress() === null);
        var_dump($order->getTotal() > 0);
        var_dump($order->hasPayments());
        var_dump($order->getBillingAddress() === null);
        var_dump($order->getShipments()->count());
        var_dump($state->getState());
        var_dump($channel->isSkippingShippingStepAllowed());
        var_dump($channel->isSkippingPaymentStepAllowed());
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

// I have to ask whether BillingAddress is required in most of the situations like free and digital product
//        if ($order->getBillingAddress() === null) {
//            return false;
//        }

        return true;
    }
}
