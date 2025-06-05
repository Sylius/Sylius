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
use Prophecy\Argument;
use spec\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;
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

final class ChoosePaymentMethodHandlerTest extends TestCase
{
    /** @var OrderRepositoryInterface|MockObject */
    private MockObject $orderRepositoryMock;

    /** @var PaymentMethodRepositoryInterface|MockObject */
    private MockObject $paymentMethodRepositoryMock;

    /** @var PaymentRepositoryInterface|MockObject */
    private MockObject $paymentRepositoryMock;

    /** @var StateMachineInterface|MockObject */
    private MockObject $stateMachineMock;

    /** @var PaymentMethodChangerInterface|MockObject */
    private MockObject $paymentMethodChangerMock;

    private ChoosePaymentMethodHandler $choosePaymentMethodHandler;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        $this->orderRepositoryMock = $this->createMock(OrderRepositoryInterface::class);
        $this->paymentMethodRepositoryMock = $this->createMock(PaymentMethodRepositoryInterface::class);
        $this->paymentRepositoryMock = $this->createMock(PaymentRepositoryInterface::class);
        $this->stateMachineMock = $this->createMock(StateMachineInterface::class);
        $this->paymentMethodChangerMock = $this->createMock(PaymentMethodChangerInterface::class);
        $this->choosePaymentMethodHandler = new ChoosePaymentMethodHandler($this->orderRepositoryMock, $this->paymentMethodRepositoryMock, $this->paymentRepositoryMock, $this->stateMachineMock, $this->paymentMethodChangerMock);
    }

    public function testAssignsChosenPaymentMethodToSpecifiedPaymentWhileCheckout(): void
    {
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        /** @var PaymentInterface|MockObject $paymentMock */
        $paymentMock = $this->createMock(PaymentInterface::class);
        /** @var PaymentMethodInterface|MockObject $paymentMethodMock */
        $paymentMethodMock = $this->createMock(PaymentMethodInterface::class);
        $this->choosePaymentMethodHandler = new ChoosePaymentMethodHandler($this->orderRepositoryMock, $this->paymentMethodRepositoryMock, $this->paymentRepositoryMock, $this->stateMachineMock, $this->paymentMethodChangerMock);
        $choosePaymentMethod = new ChoosePaymentMethod(
            orderTokenValue: 'ORDERTOKEN',
            paymentId: 123,
            paymentMethodCode: 'CASH_ON_DELIVERY_METHOD',
        );
        $this->orderRepositoryMock->expects(self::once())->method('findOneBy')->with(['tokenValue' => 'ORDERTOKEN'])->willReturn($cartMock);
        $cartMock->expects(self::once())->method('getCheckoutState')->willReturn(OrderCheckoutStates::STATE_SHIPPING_SELECTED);
        $this->stateMachineMock->expects(self::once())->method('can')->with($cartMock, OrderCheckoutTransitions::GRAPH, 'select_payment')->willReturn(true);
        $this->stateMachineMock->expects(self::once())->method('apply')->with($cartMock, OrderCheckoutTransitions::GRAPH, 'select_payment');
        $this->paymentMethodRepositoryMock->expects(self::once())->method('findOneBy')->with(['code' => 'CASH_ON_DELIVERY_METHOD'])->willReturn($paymentMethodMock);
        $cartMock->expects(self::once())->method('getId')->willReturn('111');
        $this->paymentRepositoryMock->expects(self::once())->method('findOneByOrderId')->with('123', '111')->willReturn($paymentMock);
        $cartMock->expects(self::once())->method('getState')->willReturn(OrderInterface::STATE_CART);
        $paymentMock->expects(self::once())->method('setMethod')->with($paymentMethodMock);
        self::assertSame($cartMock, $this($choosePaymentMethod));
    }

    public function testThrowsAnExceptionIfOrderWithGivenTokenHasNotBeenFound(): void
    {
        /** @var PaymentInterface|MockObject $paymentMock */
        $paymentMock = $this->createMock(PaymentInterface::class);
        $choosePaymentMethod = new ChoosePaymentMethod(
            orderTokenValue: 'ORDERTOKEN',
            paymentId: 123,
            paymentMethodCode: 'CASH_ON_DELIVERY_METHOD',
        );
        $this->orderRepositoryMock->expects(self::once())->method('findOneBy')->with(['tokenValue' => 'ORDERTOKEN'])->willReturn(null);
        $paymentMock->expects(self::never())->method('setMethod')->with($this->isInstanceOf(PaymentMethodInterface::class));
        $this->expectException(InvalidArgumentException::class);
        $this->choosePaymentMethodHandler->__invoke($choosePaymentMethod);
    }

    public function testThrowsAnExceptionIfOrderCannotHavePaymentSelected(): void
    {
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        /** @var PaymentInterface|MockObject $paymentMock */
        $paymentMock = $this->createMock(PaymentInterface::class);
        $choosePaymentMethod = new ChoosePaymentMethod(
            orderTokenValue: 'ORDERTOKEN',
            paymentId: 123,
            paymentMethodCode: 'CASH_ON_DELIVERY_METHOD',
        );
        $this->orderRepositoryMock->expects(self::once())->method('findOneBy')->with(['tokenValue' => 'ORDERTOKEN'])->willReturn($cartMock);
        $cartMock->expects(self::once())->method('getState')->willReturn(OrderInterface::STATE_CART);
        $this->paymentMethodRepositoryMock->expects(self::once())->method('findOneBy')->with(['code' => 'CASH_ON_DELIVERY_METHOD'])->willReturn(null);
        $this->stateMachineMock->expects(self::once())->method('can')->with('select_payment')->willReturn(false);
        $paymentMock->expects(self::never())->method('setMethod')->with($this->isInstanceOf(PaymentMethodInterface::class));
        $this->stateMachineMock->expects(self::never())->method('apply')->with($cartMock, OrderCheckoutTransitions::GRAPH, 'select_payment');
        $this->expectException(InvalidArgumentException::class);
        $this->choosePaymentMethodHandler->__invoke($choosePaymentMethod);
    }

    public function testThrowsAnExceptionIfPaymentMethodWithGivenCodeHasNotBeenFound(): void
    {
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        /** @var PaymentInterface|MockObject $paymentMock */
        $paymentMock = $this->createMock(PaymentInterface::class);
        $choosePaymentMethod = new ChoosePaymentMethod(
            orderTokenValue: 'ORDERTOKEN',
            paymentId: 123,
            paymentMethodCode: 'CASH_ON_DELIVERY_METHOD',
        );
        $this->orderRepositoryMock->expects(self::once())->method('findOneBy')->with(['tokenValue' => 'ORDERTOKEN'])->willReturn($cartMock);
        $cartMock->expects(self::once())->method('getState')->willReturn(OrderInterface::STATE_CART);
        $this->paymentMethodRepositoryMock->expects(self::once())->method('findOneBy')->with(['code' => 'CASH_ON_DELIVERY_METHOD'])->willReturn(null);
        $this->stateMachineMock->expects(self::once())->method('can')->with($cartMock, OrderCheckoutTransitions::GRAPH, 'select_payment')->willReturn(true);
        $paymentMock->expects(self::never())->method('setMethod')->with($this->isInstanceOf(PaymentMethodInterface::class));
        $this->stateMachineMock->expects(self::never())->method('apply')->with($cartMock, OrderCheckoutTransitions::GRAPH, 'select_payment');
        $this->expectException(InvalidArgumentException::class);
        $this->choosePaymentMethodHandler->__invoke($choosePaymentMethod);
    }

    public function testThrowsAnExceptionIfOrderedPaymentHasNotBeenFound(): void
    {
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        /** @var PaymentMethodInterface|MockObject $paymentMethodMock */
        $paymentMethodMock = $this->createMock(PaymentMethodInterface::class);
        $choosePaymentMethod = new ChoosePaymentMethod(
            orderTokenValue: 'ORDERTOKEN',
            paymentId: 123,
            paymentMethodCode: 'CASH_ON_DELIVERY_METHOD',
        );
        $this->orderRepositoryMock->expects(self::once())->method('findOneBy')->with(['tokenValue' => 'ORDERTOKEN'])->willReturn($cartMock);
        $cartMock->expects(self::once())->method('getState')->willReturn(OrderInterface::STATE_CART);
        $this->stateMachineMock->expects(self::once())->method('can')->with($cartMock, OrderCheckoutTransitions::GRAPH, 'select_payment')->willReturn(true);
        $this->paymentMethodRepositoryMock->expects(self::once())->method('findOneBy')->with(['code' => 'CASH_ON_DELIVERY_METHOD'])->willReturn($paymentMethodMock);
        $cartMock->expects(self::once())->method('getId')->willReturn('111');
        $this->paymentRepositoryMock->expects(self::once())->method('findOneByOrderId')->with('123', '111')->willReturn(null);
        $this->stateMachineMock->expects(self::never())->method('apply')->with($cartMock, OrderCheckoutTransitions::GRAPH, 'select_payment');
        $this->expectException(InvalidArgumentException::class);
        $this->choosePaymentMethodHandler->__invoke($choosePaymentMethod);
    }

    public function testThrowsAnExceptionIfPaymentIsInDifferentStateThanNew(): void
    {
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        /** @var PaymentInterface|MockObject $paymentMock */
        $paymentMock = $this->createMock(PaymentInterface::class);
        /** @var PaymentMethodInterface|MockObject $paymentMethodMock */
        $paymentMethodMock = $this->createMock(PaymentMethodInterface::class);
        $choosePaymentMethod = new ChoosePaymentMethod(
            orderTokenValue: 'ORDERTOKEN',
            paymentId: 123,
            paymentMethodCode: 'CASH_ON_DELIVERY_METHOD',
        );
        $this->orderRepositoryMock->expects(self::once())->method('findOneBy')->with(['tokenValue' => 'ORDERTOKEN'])->willReturn($cartMock);
        $this->paymentMethodRepositoryMock->expects(self::once())->method('findOneBy')->with(['code' => 'CASH_ON_DELIVERY_METHOD'])->willReturn($paymentMethodMock);
        $cartMock->expects(self::once())->method('getCheckoutState')->willReturn(OrderCheckoutStates::STATE_COMPLETED);
        $cartMock->expects(self::once())->method('getId')->willReturn('111');
        $this->paymentRepositoryMock->expects(self::once())->method('findOneByOrderId')->with('123', '111')->willReturn($paymentMock);
        $cartMock->expects(self::once())->method('getState')->willReturn(OrderInterface::STATE_FULFILLED);
        $paymentMock->expects(self::once())->method('getState')->willReturn(PaymentInterface::STATE_CANCELLED);
        $this->expectException(PaymentMethodCannotBeChangedException::class);
        $this->choosePaymentMethodHandler->__invoke($choosePaymentMethod);
    }

    public function testAssignsChosenPaymentMethodToSpecifiedPaymentAfterCheckout(): void
    {
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        $choosePaymentMethod = new ChoosePaymentMethod(
            orderTokenValue: 'ORDERTOKEN',
            paymentId: 123,
            paymentMethodCode: 'CASH_ON_DELIVERY_METHOD',
        );
        $this->orderRepositoryMock->expects(self::once())->method('findOneBy')->with(['tokenValue' => 'ORDERTOKEN'])->willReturn($cartMock);
        $cartMock->expects(self::once())->method('getState')->willReturn(OrderInterface::STATE_NEW);
        $this->paymentMethodChangerMock->changePaymentMethod('CASH_ON_DELIVERY_METHOD', 123, $cartMock);
        $this->paymentMethodRepositoryMock->expects(self::never())->method('findOneBy')->with(['code' => 'CASH_ON_DELIVERY_METHOD'])
            ->willReturn(Argument::type(PaymentMethodInterface::class))
        ;
        self::assertSame($cartMock, $this($choosePaymentMethod));
    }
}
