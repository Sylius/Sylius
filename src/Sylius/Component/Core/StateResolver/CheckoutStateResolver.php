<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\StateResolver;

use SM\Factory\FactoryInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\StateResolver\StateResolverInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class CheckoutStateResolver implements StateResolverInterface
{
    /**
     * @var FactoryInterface
     */
    private $stateMachineFactory;

    /**
     * @param FactoryInterface $stateMachineFactory
     */
    public function __construct(FactoryInterface $stateMachineFactory)
    {
        $this->stateMachineFactory = $stateMachineFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(OrderInterface $order)
    {
        $stateMachine = $this->stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH);

        if (0 === $order->getTotal() && $stateMachine->can(OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)) {
            $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT);
        }
    }
}
