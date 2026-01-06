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

namespace Tests\Sylius\Bundle\PayumBundle\Action;

use Payum\Core\Action\ActionInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\PayumBundle\Action\ResolveNextRouteAction;
use Sylius\Bundle\PayumBundle\Request\ResolveNextRoute;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;

final class ResolveNextRouteActionTest extends TestCase
{
    private ResolveNextRouteAction $resolveNextRouteAction;

    private MockObject&ResolveNextRoute $resolveNextRouteRequest;

    private MockObject&PaymentInterface $payment;

    private MockObject&OrderInterface $order;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolveNextRouteAction = new ResolveNextRouteAction();
        $this->resolveNextRouteRequest = $this->createMock(ResolveNextRoute::class);
        $this->payment = $this->createMock(PaymentInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
    }

    public function testAPayumAction(): void
    {
        self::assertInstanceOf(ActionInterface::class, $this->resolveNextRouteAction);
    }

    public function testResolvesNextRouteForCompletedPayment(): void
    {
        $this->resolveNextRouteRequest->expects(self::once())
            ->method('getFirstModel')
            ->willReturn($this->payment);

        $this->payment->expects(self::once())
            ->method('getState')
            ->willReturn(PaymentInterface::STATE_COMPLETED);

        $this->resolveNextRouteRequest->expects(self::once())
            ->method('setRouteName')
            ->with('sylius_shop_order_thank_you');

        $this->resolveNextRouteAction->execute($this->resolveNextRouteRequest);
    }

    public function testResolvesNextRouteForCancelledPayment(): void
    {
        $this->resolveNextRouteRequest->expects(self::once())
            ->method('getFirstModel')
            ->willReturn($this->payment);

        $this->payment->method('getState')->willReturn(PaymentInterface::STATE_CANCELLED);

        $this->payment->expects(self::once())->method('getOrder')->willReturn($this->order);

        $this->order->expects(self::once())->method('getTokenValue')->willReturn('qwerty');

        $this->resolveNextRouteRequest->expects(self::once())
            ->method('setRouteName')
            ->with('sylius_shop_order_show');

        $this->resolveNextRouteRequest->expects(self::once())
            ->method('setRouteParameters')
            ->with(['tokenValue' => 'qwerty']);

        $this->resolveNextRouteAction->execute($this->resolveNextRouteRequest);
    }

    public function testResolvesNextRouteForPaymentInCartState(): void
    {
        $this->resolveNextRouteRequest->expects(self::once())
            ->method('getFirstModel')
            ->willReturn($this->payment);

        $this->payment->method('getState')->willReturn(PaymentInterface::STATE_CART);

        $this->payment->expects(self::once())->method('getOrder')->willReturn($this->order);

        $this->order->expects(self::once())->method('getTokenValue')->willReturn('qwerty');

        $this->resolveNextRouteRequest->expects(self::once())
            ->method('setRouteName')
            ->with('sylius_shop_order_show');

        $this->resolveNextRouteRequest->expects(self::once())
            ->method('setRouteParameters')
            ->with(['tokenValue' => 'qwerty']);

        $this->resolveNextRouteAction->execute($this->resolveNextRouteRequest);
    }

    public function testResolvesNextRouteForFailedPayment(): void
    {
        $this->resolveNextRouteRequest->expects(self::once())
            ->method('getFirstModel')
            ->willReturn($this->payment);

        $this->payment->method('getState')->willReturn(PaymentInterface::STATE_FAILED);

        $this->payment->expects(self::once())->method('getOrder')->willReturn($this->order);

        $this->order->expects(self::once())->method('getTokenValue')->willReturn('qwerty');

        $this->resolveNextRouteRequest->expects(self::once())
            ->method('setRouteName')
            ->with('sylius_shop_order_show');

        $this->resolveNextRouteRequest->expects(self::once())
            ->method('setRouteParameters')
            ->with(['tokenValue' => 'qwerty']);

        $this->resolveNextRouteAction->execute($this->resolveNextRouteRequest);
    }

    public function testResolvesNextRouteForProcessingPayment(): void
    {
        $this->resolveNextRouteRequest->expects(self::once())
            ->method('getFirstModel')
            ->willReturn($this->payment);

        $this->payment->method('getState')->willReturn(PaymentInterface::STATE_PROCESSING);

        $this->payment->expects(self::once())->method('getOrder')->willReturn($this->order);

        $this->order->expects(self::once())->method('getTokenValue')->willReturn('qwerty');

        $this->resolveNextRouteRequest->expects(self::once())
            ->method('setRouteName')
            ->with('sylius_shop_order_show');

        $this->resolveNextRouteRequest->expects(self::once())
            ->method('setRouteParameters')
            ->with(['tokenValue' => 'qwerty']);

        $this->resolveNextRouteAction->execute($this->resolveNextRouteRequest);
    }
}
