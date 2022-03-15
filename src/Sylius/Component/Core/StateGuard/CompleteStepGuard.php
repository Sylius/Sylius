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

use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\OrderCheckoutTransitions;

class CompleteStepGuard
{
    public function __construct(
        private FactoryInterface $stateMachineFactory,
    ) {
    }

    /**
     * You can skip shipping step and payment step when order is free AND not require shipping.
     */
    public function isSatisfiedBy(OrderInterface $order): bool
    {
        $stateMachine = $this->stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH);

        return $order->isShippingRequired() === false
            && $order->getTotal() === 0
            && in_array(
                $stateMachine->getState(),
                [
                    OrderCheckoutStates::STATE_ADDRESSED,
                    OrderCheckoutStates::STATE_PAYMENT_SELECTED,
                ]
            );
    }
}
