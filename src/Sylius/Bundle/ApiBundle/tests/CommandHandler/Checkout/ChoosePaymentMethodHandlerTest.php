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

namespace Tests\Sylius\Bundle\ApiBundle\CommandHandler\Checkout;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\ApiBundle\Changer\PaymentMethodChangerInterface;
use Sylius\Bundle\ApiBundle\Command\Checkout\ChoosePaymentMethod;
use Sylius\Bundle\ApiBundle\CommandHandler\Checkout\ChoosePaymentMethodHandler;
use Sylius\Bundle\ApiBundle\Exception\PaymentMethodCannotBeChangedException;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Tests\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;

final class ChoosePaymentMethodHandlerTest extends TestCase
{
    private MockObject&OrderRepositoryInterface $orderRepository;

    private MockObject&PaymentMethodRepositoryInterface $paymentMethodRepository;

    private MockObject&PaymentRepositoryInterface $paymentRepository;

    private MockObject&StateMachineInterface $stateMachine;

    private MockObject&PaymentMethodChangerInterface $paymentMethodChanger;

    private ChoosePaymentMethodHandler $handler;

    private MockObject&OrderInterface $cart;

    private MockObject&PaymentInterface $payment;

    private MockObject&PaymentMethodInterface $paymentMethod;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->paymentMethodRepository = $this->createMock(PaymentMethodRepositoryInterface::class);
        $this->paymentRepository = $this->createMock(PaymentRepositoryInterface::class);
        $this->stateMachine = $this->createMock(StateMachineInterface::class);
        $this->paymentMethodChanger = $this->createMock(PaymentMethodChangerInterface::class);
        $this->handler = new ChoosePaymentMethodHandler(
            $this->orderRepository,
            $this->paymentMethodRepository,
            $this->paymentRepository,
            $this->stateMachine,
            $this->paymentMethodChanger,
        );
        $this->cart = $this->createMock(OrderInterface::class);
        $this->payment = $this->createMock(PaymentInterface::class);
        $this->paymentMethod = $this->createMock(PaymentMethodInterface::class);
    }

    public function testAssignsChosenPaymentMethodToSpecifiedPaymentWhileCheckout(): void
    {
        $this->handler = new ChoosePaymentMethodHandler(
            $this->orderRepository,
            $this->paymentMethodRepository,
            $this->paymentRepository,
            $this->stateMachine,
            $this->paymentMethodChanger,
        );
        $choosePaymentMethod = new ChoosePaymentMethod(
            orderTokenValue: 'ORDERTOKEN',
            paymentId: 123,
            paymentMethodCode: 'CASH_ON_DELIVERY_METHOD',
        );
        $this->orderRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'ORDERTOKEN'])
            ->willReturn($this->cart);
        $this->cart->method('getCheckoutState')->willReturn(OrderCheckoutStates::STATE_SHIPPING_SELECTED);
        $this->stateMachine->expects(self::once())
            ->method('can')
            ->with($this->cart, OrderCheckoutTransitions::GRAPH, 'select_payment')
            ->willReturn(true);
        $this->stateMachine->expects(self::once())
            ->method('apply')
            ->with($this->cart, OrderCheckoutTransitions::GRAPH, 'select_payment');
        $this->paymentMethodRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => 'CASH_ON_DELIVERY_METHOD'])
            ->willReturn($this->paymentMethod);
        $this->cart->expects(self::once())->method('getId')->willReturn('111');
        $this->paymentRepository->expects(self::once())
            ->method('findOneByOrderId')
            ->with('123', '111')
            ->willReturn($this->payment);
        $this->cart->method('getState')->willReturn(OrderInterface::STATE_CART);
        $this->payment->expects(self::once())->method('setMethod')->with($this->paymentMethod);
        self::assertSame($this->cart, $this->handler->__invoke($choosePaymentMethod));
    }

    public function testThrowsAnExceptionIfOrderWithGivenTokenHasNotBeenFound(): void
    {
        $choosePaymentMethod = new ChoosePaymentMethod(
            orderTokenValue: 'ORDERTOKEN',
            paymentId: 123,
            paymentMethodCode: 'CASH_ON_DELIVERY_METHOD',
        );
        $this->orderRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'ORDERTOKEN'])
            ->willReturn(null);
        $this->payment->expects(self::never())
            ->method('setMethod')
            ->with($this->isInstanceOf(PaymentMethodInterface::class));
        self::expectException(InvalidArgumentException::class);
        $this->handler->__invoke($choosePaymentMethod);
    }

    public function testThrowsAnExceptionIfOrderCannotHavePaymentSelected(): void
    {
        $choosePaymentMethod = new ChoosePaymentMethod(
            orderTokenValue: 'ORDERTOKEN',
            paymentId: 123,
            paymentMethodCode: 'CASH_ON_DELIVERY_METHOD',
        );
        $this->orderRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'ORDERTOKEN'])
            ->willReturn($this->cart);
        $this->cart->expects(self::once())->method('getState')->willReturn(OrderInterface::STATE_CART);
        $this->paymentMethodRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => 'CASH_ON_DELIVERY_METHOD'])
            ->willReturn(null);
        $this->stateMachine->method('can')->with('select_payment')->willReturn(false);
        $this->payment->expects(self::never())
            ->method('setMethod')
            ->with($this->isInstanceOf(PaymentMethodInterface::class));
        $this->stateMachine->expects(self::never())
            ->method('apply')
            ->with($this->cart, OrderCheckoutTransitions::GRAPH, 'select_payment');
        self::expectException(InvalidArgumentException::class);
        $this->handler->__invoke($choosePaymentMethod);
    }

    public function testThrowsAnExceptionIfPaymentMethodWithGivenCodeHasNotBeenFound(): void
    {
        $choosePaymentMethod = new ChoosePaymentMethod(
            orderTokenValue: 'ORDERTOKEN',
            paymentId: 123,
            paymentMethodCode: 'CASH_ON_DELIVERY_METHOD',
        );
        $this->orderRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'ORDERTOKEN'])
            ->willReturn($this->cart);
        $this->cart->expects(self::once())->method('getState')->willReturn(OrderInterface::STATE_CART);
        $this->paymentMethodRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => 'CASH_ON_DELIVERY_METHOD'])
            ->willReturn(null);
        $this->stateMachine->method('can')
            ->with($this->cart, OrderCheckoutTransitions::GRAPH, 'select_payment')
            ->willReturn(true);
        $this->payment->expects(self::never())
            ->method('setMethod')
            ->with($this->isInstanceOf(PaymentMethodInterface::class));
        $this->stateMachine->expects(self::never())
            ->method('apply')
            ->with($this->cart, OrderCheckoutTransitions::GRAPH, 'select_payment');
        self::expectException(InvalidArgumentException::class);
        $this->handler->__invoke($choosePaymentMethod);
    }

    public function testThrowsAnExceptionIfOrderedPaymentHasNotBeenFound(): void
    {
        $choosePaymentMethod = new ChoosePaymentMethod(
            orderTokenValue: 'ORDERTOKEN',
            paymentId: 123,
            paymentMethodCode: 'CASH_ON_DELIVERY_METHOD',
        );
        $this->orderRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'ORDERTOKEN'])
            ->willReturn($this->cart);
        $this->cart->expects(self::once())->method('getState')->willReturn(OrderInterface::STATE_CART);
        $this->stateMachine->method('can')
            ->with($this->cart, OrderCheckoutTransitions::GRAPH, 'select_payment')
            ->willReturn(true);
        $this->paymentMethodRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => 'CASH_ON_DELIVERY_METHOD'])
            ->willReturn($this->paymentMethod);
        $this->cart->expects(self::once())->method('getId')->willReturn('111');
        $this->paymentRepository->expects(self::once())
            ->method('findOneByOrderId')
            ->with('123', '111')
            ->willReturn(null);
        $this->stateMachine->expects(self::never())
            ->method('apply')
            ->with($this->cart, OrderCheckoutTransitions::GRAPH, 'select_payment');
        self::expectException(InvalidArgumentException::class);
        $this->handler->__invoke($choosePaymentMethod);
    }

    public function testThrowsAnExceptionIfPaymentIsInDifferentStateThanNew(): void
    {
        $choosePaymentMethod = new ChoosePaymentMethod(
            orderTokenValue: 'ORDERTOKEN',
            paymentId: 123,
            paymentMethodCode: 'CASH_ON_DELIVERY_METHOD',
        );
        $this->orderRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'ORDERTOKEN'])
            ->willReturn($this->cart);
        $this->paymentMethodRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => 'CASH_ON_DELIVERY_METHOD'])->willReturn($this->paymentMethod);
        $this->cart->method('getCheckoutState')->willReturn(OrderCheckoutStates::STATE_COMPLETED);
        $this->cart->method('getId')->willReturn('111');
        $this->paymentRepository->expects(self::once())
            ->method('findOneByOrderId')->with('123', '111')
            ->willReturn($this->payment);
        $this->cart->method('getState')->willReturn(OrderInterface::STATE_FULFILLED);
        $this->payment->method('getState')->willReturn(PaymentInterface::STATE_CANCELLED);
        self::expectException(PaymentMethodCannotBeChangedException::class);
        $this->handler->__invoke($choosePaymentMethod);
    }

    public function testAssignsChosenPaymentMethodToSpecifiedPaymentAfterCheckout(): void
    {
        $choosePaymentMethod = new ChoosePaymentMethod(
            orderTokenValue: 'ORDERTOKEN',
            paymentId: 123,
            paymentMethodCode: 'CASH_ON_DELIVERY_METHOD',
        );
        $this->orderRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'ORDERTOKEN'])
            ->willReturn($this->cart);
        $this->cart->expects(self::once())->method('getState')->willReturn(OrderInterface::STATE_NEW);
        $this->paymentMethodChanger->changePaymentMethod(
            'CASH_ON_DELIVERY_METHOD',
            123,
            $this->cart,
        );
        $this->paymentMethodRepository->expects(self::never())->method('findOneBy');
        self::assertSame($this->cart, $this->handler->__invoke($choosePaymentMethod));
    }
}
