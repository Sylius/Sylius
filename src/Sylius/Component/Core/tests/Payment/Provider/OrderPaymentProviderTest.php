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

namespace Tests\Sylius\Component\Core\Payment\Provider;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Payment\Exception\NotProvidedOrderPaymentException;
use Sylius\Component\Core\Payment\Provider\OrderPaymentProvider;
use Sylius\Component\Core\Payment\Provider\OrderPaymentProviderInterface;
use Sylius\Component\Payment\Exception\UnresolvedDefaultPaymentMethodException;
use Sylius\Component\Payment\Factory\PaymentFactoryInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Sylius\Component\Payment\Resolver\DefaultPaymentMethodResolverInterface;

final class OrderPaymentProviderTest extends TestCase
{
    private DefaultPaymentMethodResolverInterface&MockObject $defaultPaymentMethodResolver;

    private MockObject&PaymentFactoryInterface $paymentFactory;

    private MockObject&StateMachineInterface $stateMachine;

    private MockObject&OrderInterface $order;

    private MockObject&PaymentInterface $lastPayment;

    private MockObject&PaymentInterface $newPayment;

    private MockObject&PaymentMethodInterface $paymentMethod;

    private OrderPaymentProvider $provider;

    protected function setUp(): void
    {
        $this->defaultPaymentMethodResolver = $this->createMock(DefaultPaymentMethodResolverInterface::class);
        $this->paymentFactory = $this->createMock(PaymentFactoryInterface::class);
        $this->stateMachine = $this->createMock(StateMachineInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
        $this->lastPayment = $this->createMock(PaymentInterface::class);
        $this->newPayment = $this->createMock(PaymentInterface::class);
        $this->paymentMethod = $this->createMock(PaymentMethodInterface::class);
        $this->provider = new OrderPaymentProvider(
            $this->defaultPaymentMethodResolver,
            $this->paymentFactory,
            $this->stateMachine,
        );
    }

    public function testShouldImplementOrderPaymentProviderInterface(): void
    {
        $this->assertInstanceOf(OrderPaymentProviderInterface::class, $this->provider);
    }

    public function testShouldProvidePaymentInConfiguredStateWithPaymentMethodFromLastCancelledPayment(): void
    {
        $this->order->expects($this->once())->method('getTotal')->willReturn(1000);
        $this->order->expects($this->once())->method('getCurrencyCode')->willReturn('USD');
        $this->order
            ->expects($this->once())
            ->method('getLastPayment')
            ->with(PaymentInterface::STATE_CANCELLED)
            ->willReturn($this->lastPayment);
        $this->lastPayment->expects($this->once())->method('getMethod')->willReturn($this->paymentMethod);
        $this->paymentFactory
            ->expects($this->once())
            ->method('createWithAmountAndCurrencyCode')
            ->with(1000, 'USD')
            ->willReturn($this->newPayment);
        $this->defaultPaymentMethodResolver
            ->expects($this->once())
            ->method('getDefaultPaymentMethod')
            ->with($this->newPayment)
            ->willReturn($this->paymentMethod);
        $this->newPayment->expects($this->once())->method('setMethod')->with($this->paymentMethod);
        $this->newPayment->expects($this->once())->method('getState')->willReturn(PaymentInterface::STATE_CART);
        $this->newPayment->expects($this->once())->method('setOrder')->with($this->order);
        $this->stateMachine
            ->expects($this->once())
            ->method('getTransitionToState')
            ->with($this->newPayment, PaymentTransitions::GRAPH, PaymentInterface::STATE_NEW)
            ->willReturn(PaymentTransitions::TRANSITION_CREATE);
        $this->stateMachine
            ->expects($this->once())
            ->method('apply')
            ->with($this->newPayment, PaymentTransitions::GRAPH, PaymentTransitions::TRANSITION_CREATE);

        $this->assertSame(
            $this->newPayment,
            $this->provider->provideOrderPayment($this->order, PaymentInterface::STATE_NEW),
        );
    }

    public function testShouldProvidePaymentInConfiguredStateWithPaymentMethodFromLastFailedPayment(): void
    {
        $this->order->expects($this->once())->method('getTotal')->willReturn(1000);
        $this->order->expects($this->once())->method('getCurrencyCode')->willReturn('USD');
        $this->order
            ->expects($this->exactly(2))
            ->method('getLastPayment')
            ->willReturnMap([
                [PaymentInterface::STATE_CANCELLED, null],
                [PaymentInterface::STATE_FAILED, $this->lastPayment],
            ]);
        $this->lastPayment->expects($this->once())->method('getMethod')->willReturn($this->paymentMethod);
        $this->paymentFactory
            ->expects($this->once())
            ->method('createWithAmountAndCurrencyCode')
            ->with(1000, 'USD')
            ->willReturn($this->newPayment);
        $this->defaultPaymentMethodResolver
            ->expects($this->once())
            ->method('getDefaultPaymentMethod')
            ->with($this->newPayment)
            ->willReturn($this->paymentMethod);
        $this->newPayment->expects($this->once())->method('setMethod')->with($this->paymentMethod);
        $this->newPayment->expects($this->once())->method('getState')->willReturn(PaymentInterface::STATE_CART);
        $this->newPayment->expects($this->once())->method('setOrder')->with($this->order);
        $this->stateMachine
            ->expects($this->once())
            ->method('getTransitionToState')
            ->with($this->newPayment, PaymentTransitions::GRAPH, PaymentInterface::STATE_NEW)
            ->willReturn(PaymentTransitions::TRANSITION_CREATE);
        $this->stateMachine
            ->expects($this->once())
            ->method('apply')
            ->with($this->newPayment, PaymentTransitions::GRAPH, PaymentTransitions::TRANSITION_CREATE);

        $this->assertSame(
            $this->newPayment,
            $this->provider->provideOrderPayment($this->order, PaymentInterface::STATE_NEW),
        );
    }

    public function testShouldProvidePaymentInConfiguredStateWithDefaultPaymentMethod(): void
    {
        $this->order->expects($this->once())->method('getTotal')->willReturn(1000);
        $this->order->expects($this->once())->method('getCurrencyCode')->willReturn('USD');
        $this->order
            ->expects($this->exactly(2))
            ->method('getLastPayment')
            ->willReturnMap([
                [PaymentInterface::STATE_CANCELLED, null],
                [PaymentInterface::STATE_FAILED, null],
            ]);
        $this->paymentFactory
            ->expects($this->once())
            ->method('createWithAmountAndCurrencyCode')
            ->with(1000, 'USD')
            ->willReturn($this->newPayment);
        $this->defaultPaymentMethodResolver
            ->expects($this->once())
            ->method('getDefaultPaymentMethod')
            ->with($this->newPayment)
            ->willReturn($this->paymentMethod);
        $this->newPayment->expects($this->once())->method('setMethod')->with($this->paymentMethod);
        $this->newPayment->expects($this->once())->method('getState')->willReturn(PaymentInterface::STATE_CART);
        $this->newPayment->expects($this->once())->method('setOrder')->with($this->order);
        $this->stateMachine
            ->expects($this->once())
            ->method('getTransitionToState')
            ->with($this->newPayment, PaymentTransitions::GRAPH, PaymentInterface::STATE_NEW)
            ->willReturn(PaymentTransitions::TRANSITION_CREATE);
        $this->stateMachine
            ->expects($this->once())
            ->method('apply')
            ->with($this->newPayment, PaymentTransitions::GRAPH, PaymentTransitions::TRANSITION_CREATE);

        $this->assertSame(
            $this->newPayment,
            $this->provider->provideOrderPayment($this->order, PaymentInterface::STATE_NEW),
        );
    }

    public function testShouldNotApplyAnyTransitionIfTargetStateIsTheSameAsNewPayment(): void
    {
        $this->order->expects($this->once())->method('getTotal')->willReturn(1000);
        $this->order->expects($this->once())->method('getCurrencyCode')->willReturn('USD');
        $this->order
            ->expects($this->exactly(2))
            ->method('getLastPayment')
            ->willReturnMap([
                [PaymentInterface::STATE_CANCELLED, null],
                [PaymentInterface::STATE_FAILED, null],
            ]);
        $this->paymentFactory
            ->expects($this->once())
            ->method('createWithAmountAndCurrencyCode')
            ->with(1000, 'USD')
            ->willReturn($this->newPayment);
        $this->defaultPaymentMethodResolver
            ->expects($this->once())
            ->method('getDefaultPaymentMethod')
            ->with($this->newPayment)
            ->willReturn($this->paymentMethod);
        $this->newPayment->expects($this->once())->method('setMethod')->with($this->paymentMethod);
        $this->newPayment->expects($this->once())->method('getState')->willReturn(PaymentInterface::STATE_NEW);
        $this->newPayment->expects($this->once())->method('setOrder')->with($this->order);
        $this->stateMachine
            ->expects($this->never())
            ->method('getTransitionToState')
            ->with($this->anything());

        $this->assertSame(
            $this->newPayment,
            $this->provider->provideOrderPayment($this->order, PaymentInterface::STATE_NEW),
        );
    }

    public function testShouldThrowExceptionIfPaymentMethodCannotBeResolvedForProvidedPayment(): void
    {
        $this->expectException(NotProvidedOrderPaymentException::class);
        $this->order->expects($this->once())->method('getTotal')->willReturn(1000);
        $this->order->expects($this->once())->method('getCurrencyCode')->willReturn('USD');
        $this->order
            ->expects($this->exactly(2))
            ->method('getLastPayment')
            ->willReturnMap([
                [PaymentInterface::STATE_CANCELLED, null],
                [PaymentInterface::STATE_FAILED, $this->lastPayment],
            ]);
        $this->paymentFactory
            ->expects($this->once())
            ->method('createWithAmountAndCurrencyCode')
            ->with(1000, 'USD')
            ->willReturn($this->newPayment);
        $this->defaultPaymentMethodResolver
            ->expects($this->once())
            ->method('getDefaultPaymentMethod')
            ->with($this->newPayment)
            ->willThrowException(new UnresolvedDefaultPaymentMethodException());
        $this->lastPayment->expects($this->once())->method('getMethod')->willReturn(null);

        $this->provider->provideOrderPayment($this->order, PaymentInterface::STATE_NEW);
    }
}
