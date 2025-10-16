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
use Sylius\Component\Core\Checker\OrderPaymentMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Checker\OrderShippingMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\StateResolver\CheckoutStateResolver;
use Sylius\Component\Order\StateResolver\StateResolverInterface;

final class CheckoutStateResolverTest extends TestCase
{
    private MockObject&StateMachineInterface $stateMachine;

    private MockObject&OrderPaymentMethodSelectionRequirementCheckerInterface $orderPaymentMethodSelectionRequirementChecker;

    private MockObject&OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker;

    private MockObject&OrderInterface $order;

    private CheckoutStateResolver $stateResolver;

    protected function setUp(): void
    {
        $this->stateMachine = $this->createMock(StateMachineInterface::class);
        $this->orderPaymentMethodSelectionRequirementChecker = $this->createMock(
            OrderPaymentMethodSelectionRequirementCheckerInterface::class,
        );
        $this->orderShippingMethodSelectionRequirementChecker = $this->createMock(
            OrderShippingMethodSelectionRequirementCheckerInterface::class,
        );
        $this->order = $this->createMock(OrderInterface::class);
        $this->stateResolver = new CheckoutStateResolver(
            $this->stateMachine,
            $this->orderPaymentMethodSelectionRequirementChecker,
            $this->orderShippingMethodSelectionRequirementChecker,
        );
    }

    public function testShouldImplementStateResolverInterface(): void
    {
        $this->assertInstanceOf(StateResolverInterface::class, $this->stateResolver);
    }

    public function testShouldApplyTransitionSkipShippingAndSkipPaymentIfShippingMethodSelectionIsNotRequiredAndPaymentMethodSelectionIsNotRequiredAndThisTransitionsArePossible(): void
    {
        $this->orderShippingMethodSelectionRequirementChecker
            ->expects($this->once())
            ->method('isShippingMethodSelectionRequired')
            ->with($this->order)
            ->willReturn(false);
        $this->orderPaymentMethodSelectionRequirementChecker
            ->expects($this->once())
            ->method('isPaymentMethodSelectionRequired')
            ->with($this->order)
            ->willReturn(false);
        $this->stateMachine
            ->expects($this->exactly(2))
            ->method('can')
            ->willReturnMap([
                [$this->order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING, true],
                [$this->order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT, true],
            ]);
        $applyInvokedCount = $this->exactly(2);
        $this->stateMachine
            ->expects($applyInvokedCount)
            ->method('apply')
            ->willReturnCallback(function ($subject, $graph, $transition) use ($applyInvokedCount): void {
                if ($applyInvokedCount->numberOfInvocations() === 1) {
                    $this->assertSame(OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING, $transition);
                }
                if ($applyInvokedCount->numberOfInvocations() === 2) {
                    $this->assertSame(OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT, $transition);
                }
            });

        $this->stateResolver->resolve($this->order);
    }

    public function testShouldApplyTransitionSkipShippingIfShippingMethodSelectionIsNotRequiredAndThisTransitionIsPossible(): void
    {
        $this->orderShippingMethodSelectionRequirementChecker
            ->expects($this->once())
            ->method('isShippingMethodSelectionRequired')
            ->with($this->order)
            ->willReturn(false);
        $this->orderPaymentMethodSelectionRequirementChecker
            ->expects($this->once())
            ->method('isPaymentMethodSelectionRequired')
            ->with($this->order)
            ->willReturn(true);
        $this->stateMachine
            ->expects($this->once())
            ->method('can')
            ->with($this->order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)
            ->willReturn(true);
        $this->stateMachine
            ->expects($this->once())
            ->method('apply')
            ->with($this->order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING);

        $this->stateResolver->resolve($this->order);
    }

    public function testShouldNotApplySkipShippingTransitionIfShippingMethodIsRequired(): void
    {
        $this->orderShippingMethodSelectionRequirementChecker
            ->expects($this->once())
            ->method('isShippingMethodSelectionRequired')
            ->with($this->order)
            ->willReturn(true);
        $this->orderPaymentMethodSelectionRequirementChecker
            ->expects($this->once())
            ->method('isPaymentMethodSelectionRequired')
            ->with($this->order)
            ->willReturn(true);
        $this->stateMachine
            ->expects($this->never())
            ->method('can')
            ->with($this->anything());
        $this->stateMachine->expects($this->never())->method('apply')->with($this->anything());

        $this->stateResolver->resolve($this->order);
    }

    public function testShouldApplyTransitionSkipPaymentIfPaymentMethodSelectionIsNotRequiredAndThisTransitionIsPossible(): void
    {
        $this->orderShippingMethodSelectionRequirementChecker
            ->expects($this->once())
            ->method('isShippingMethodSelectionRequired')
            ->with($this->order)
            ->willReturn(true);
        $this->orderPaymentMethodSelectionRequirementChecker
            ->expects($this->once())
            ->method('isPaymentMethodSelectionRequired')
            ->with($this->order)
            ->willReturn(false);
        $this->stateMachine
            ->expects($this->once())
            ->method('can')
            ->with($this->order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)
            ->willReturn(true);
        $this->stateMachine
            ->expects($this->once())
            ->method('apply')
            ->with($this->order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT);

        $this->stateResolver->resolve($this->order);
    }
}
