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

namespace Tests\Sylius\Bundle\ApiBundle\Applicator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\ApiBundle\Applicator\OrderStateMachineTransitionApplicator;
use Sylius\Bundle\ApiBundle\Exception\StateMachineTransitionFailedException;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\OrderTransitions;

final class OrderStateMachineTransitionApplicatorTest extends TestCase
{
    private MockObject&StateMachineInterface $stateMachine;

    private OrderStateMachineTransitionApplicator $orderStateMachineTransitionApplicator;

    private MockObject&OrderInterface $order;

    public function testCancelsOrder(): void
    {
        $this->stateMachine->expects(self::once())
            ->method('can')
            ->with(
                $this->order,
                OrderTransitions::GRAPH,
                OrderTransitions::TRANSITION_CANCEL,
            )->willReturn(true);

        $this->stateMachine->expects(self::once())
            ->method('apply')
            ->with(
                $this->order,
                OrderTransitions::GRAPH,
                OrderTransitions::TRANSITION_CANCEL,
            );

        $this->orderStateMachineTransitionApplicator->cancel($this->order);
    }

    public function testThrowExceptionIfCannotCancelOrder(): void
    {
        $this->stateMachine->expects(self::once())
            ->method('can')
            ->with(
                $this->order,
                OrderTransitions::GRAPH,
                OrderTransitions::TRANSITION_CANCEL,
            )->willReturn(false);

        $this->stateMachine->expects(self::never())
            ->method('apply')
            ->with(
                $this->order,
                OrderTransitions::GRAPH,
                OrderTransitions::TRANSITION_CANCEL,
            );

        self::expectException(StateMachineTransitionFailedException::class);

        $this->orderStateMachineTransitionApplicator->cancel($this->order);
    }

    protected function setUp(): void
    {
        $this->stateMachine = $this->createMock(StateMachineInterface::class);
        $this->orderStateMachineTransitionApplicator = new OrderStateMachineTransitionApplicator($this->stateMachine);
        $this->order = $this->createMock(OrderInterface::class);
    }
}
