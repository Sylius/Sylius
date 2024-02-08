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

use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\OrderTransitions;

/** @experimental */
final class OrderStateMachineTransitionApplicator implements OrderStateMachineTransitionApplicatorInterface
{
    public function __construct(private StateMachineFactoryInterface $stateMachineFactory)
    {
    }

    public function cancel(OrderInterface $data): OrderInterface
    {
        $this->applyTransition($data, OrderTransitions::TRANSITION_CANCEL);

        return $data;
    }

    private function applyTransition(OrderInterface $order, string $transition): void
    {
        $stateMachine = $this->stateMachineFactory->get($order, OrderTransitions::GRAPH);
        $stateMachine->apply($transition);
    }
}
