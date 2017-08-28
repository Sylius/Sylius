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

namespace Sylius\Component\Core\StateResolver;

use SM\Factory\FactoryInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\OrderShippingStates;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\OrderTransitions;
use Sylius\Component\Order\StateResolver\StateResolverInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class OrderStateResolver implements StateResolverInterface
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
    public function resolve(OrderInterface $order): void
    {
        $stateMachine = $this->stateMachineFactory->get($order, OrderTransitions::GRAPH);

        if ($this->canOrderBeFulfilled($order) && $stateMachine->can(OrderTransitions::TRANSITION_FULFILL)) {
            $stateMachine->apply(OrderTransitions::TRANSITION_FULFILL);
        }
    }

    /**
     * @param OrderInterface $order
     *
     * @return bool
     */
    private function canOrderBeFulfilled(OrderInterface $order)
    {
        return
            OrderPaymentStates::STATE_PAID === $order->getPaymentState() &&
            OrderShippingStates::STATE_SHIPPED === $order->getShippingState()
        ;
    }
}
