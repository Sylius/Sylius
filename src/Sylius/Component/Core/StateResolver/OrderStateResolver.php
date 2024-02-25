<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\StateResolver;

use SM\Factory\FactoryInterface;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Abstraction\StateMachine\WinzouStateMachineAdapter;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\OrderShippingStates;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Order\OrderTransitions;
use Sylius\Component\Order\StateResolver\StateResolverInterface;
use Webmozart\Assert\Assert;

final class OrderStateResolver implements StateResolverInterface
{
    public function __construct(private FactoryInterface|StateMachineInterface $stateMachineFactory)
    {
        if ($this->stateMachineFactory instanceof FactoryInterface) {
            trigger_deprecation(
                'sylius/core',
                '1.13',
                sprintf(
                    'Passing an instance of "%s" as the first argument is deprecated. It will accept only instances of "%s" in Sylius 2.0.',
                    FactoryInterface::class,
                    StateMachineInterface::class,
                ),
            );
        }
    }

    public function resolve(BaseOrderInterface $order): void
    {
        Assert::isInstanceOf($order, OrderInterface::class);
        $stateMachine = $this->getStateMachine();

        if ($this->canOrderBeFulfilled($order) && $stateMachine->can($order, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_FULFILL)) {
            $stateMachine->apply($order, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_FULFILL);
        }
    }

    private function canOrderBeFulfilled(OrderInterface $order): bool
    {
        return
            (OrderPaymentStates::STATE_PAID === $order->getPaymentState() ||
            OrderPaymentStates::STATE_PARTIALLY_REFUNDED === $order->getPaymentState()) &&
            OrderShippingStates::STATE_SHIPPED === $order->getShippingState()
        ;
    }

    private function getStateMachine(): StateMachineInterface
    {
        if ($this->stateMachineFactory instanceof FactoryInterface) {
            return new WinzouStateMachineAdapter($this->stateMachineFactory);
        }

        return $this->stateMachineFactory;
    }
}
