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

namespace Tests\Sylius\Component\Core\StateResolver;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\OrderShippingStates;
use Sylius\Component\Core\StateResolver\OrderStateResolver;
use Sylius\Component\Order\OrderTransitions;
use Sylius\Component\Order\StateResolver\StateResolverInterface;

final class OrderStateResolverTest extends TestCase
{
    private MockObject&StateMachineInterface $stateMachine;

    private MockObject&OrderInterface $order;

    private OrderStateResolver $stateResolver;

    protected function setUp(): void
    {
        $this->stateMachine = $this->createMock(StateMachineInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
        $this->stateResolver = new OrderStateResolver($this->stateMachine);
    }

    public function testShouldImplementStateResolverInterface(): void
    {
        $this->assertInstanceOf(StateResolverInterface::class, $this->stateResolver);
    }

    public function testShouldMarksOrderAsFulfilledWhenItsPaidForAndHasBeenShipped(): void
    {
        $this->order->expects($this->once())->method('getShippingState')->willReturn(OrderShippingStates::STATE_SHIPPED);
        $this->order->expects($this->once())->method('getPaymentState')->willReturn(OrderPaymentStates::STATE_PAID);
        $this->stateMachine
            ->expects($this->once())
            ->method('can')
            ->with($this->order, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_FULFILL)
            ->willReturn(true);
        $this->stateMachine
            ->expects($this->once())
            ->method('apply')
            ->with($this->order, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_FULFILL);

        $this->stateResolver->resolve($this->order);
    }

    public function testShouldMarksOrderAsFulfilledWhenItsPartiallyRefundedAndHasBeenShipped(): void
    {
        $this->order->expects($this->once())->method('getShippingState')->willReturn(OrderShippingStates::STATE_SHIPPED);
        $this->order->expects($this->exactly(2))->method('getPaymentState')->willReturn(OrderPaymentStates::STATE_PARTIALLY_REFUNDED);
        $this->stateMachine
            ->expects($this->once())
            ->method('can')
            ->with($this->order, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_FULFILL)
            ->willReturn(true);
        $this->stateMachine
            ->expects($this->once())
            ->method('apply')
            ->with($this->order, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_FULFILL);

        $this->stateResolver->resolve($this->order);
    }

    public function testShouldNotMarkOrderAsFulfilledWhenItHasBeenPaidButNotShipped(): void
    {
        $this->order->expects($this->once())->method('getShippingState')->willreturn(OrderShippingStates::STATE_READY);
        $this->order->expects($this->once())->method('getPaymentState')->willReturn(OrderPaymentStates::STATE_PAID);
        $this->stateMachine
            ->expects($this->never())
            ->method('can')
            ->with($this->order, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_FULFILL);
        $this->stateMachine
            ->expects($this->never())
            ->method('apply')
            ->with($this->order, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_FULFILL);

        $this->stateResolver->resolve($this->order);
    }

    public function testShouldNotMarkOrderAsFulfilledWhenItHasBeenShippedButNotPaid(): void
    {
        $this->stateMachine
            ->expects($this->never())
            ->method('can')
            ->with($this->order, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_FULFILL);
        $this->stateMachine
            ->expects($this->never())
            ->method('apply')
            ->with($this->order, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_FULFILL);

        $this->stateResolver->resolve($this->order);
    }
}
