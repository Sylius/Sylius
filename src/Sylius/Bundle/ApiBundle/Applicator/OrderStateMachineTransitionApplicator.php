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

namespace Sylius\Bundle\ApiBundle\Applicator;

use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\ApiBundle\Exception\StateMachineTransitionFailedException;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\OrderTransitions;

final class OrderStateMachineTransitionApplicator implements OrderStateMachineTransitionApplicatorInterface
{
    public function __construct(private StateMachineInterface $stateMachine)
    {
    }

    public function cancel(OrderInterface $data): OrderInterface
    {
        $this->applyTransition($data, OrderTransitions::TRANSITION_CANCEL);

        return $data;
    }

    private function applyTransition(OrderInterface $order, string $transition): void
    {
        if (false === $this->stateMachine->can($order, OrderTransitions::GRAPH, $transition)) {
            throw new StateMachineTransitionFailedException('Cannot cancel the order.');
        }

        $this->stateMachine->apply($order, OrderTransitions::GRAPH, $transition);
    }
}
