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

namespace Tests\Sylius\Bundle\ApiBundle\Changer;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Changer\PaymentMethodChanger;
use Sylius\Bundle\ApiBundle\Exception\PaymentMethodCannotBeChangedException;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;

final class PaymentMethodChangerTest extends TestCase
{
    private MockObject&PaymentRepositoryInterface $paymentRepository;

    private MockObject&PaymentMethodRepositoryInterface $paymentMethodRepository;

    private PaymentMethodChanger $paymentMethodChanger;

    private MockObject&OrderInterface $order;

    private MockObject&PaymentInterface $payment;

    private MockObject&PaymentMethodInterface $paymentMethod;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentRepository = $this->createMock(PaymentRepositoryInterface::class);
        $this->paymentMethodRepository = $this->createMock(PaymentMethodRepositoryInterface::class);
        $this->paymentMethodChanger = new PaymentMethodChanger(
            $this->paymentRepository,
            $this->paymentMethodRepository,
        );
        $this->order = $this->createMock(OrderInterface::class);
        $this->paymentMethod = $this->createMock(PaymentMethodInterface::class);
        $this->payment = $this->createMock(PaymentInterface::class);
    }

    public function testThrowsAnExceptionIfPaymentIsInDifferentStateThanNew(): void
    {
        $this->paymentMethodRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => 'CASH_ON_DELIVERY_METHOD'])
            ->willReturn($this->paymentMethod);

        $this->order
            ->expects(self::once())
            ->method('getId')
            ->willReturn('444');

        $this->paymentRepository
            ->expects(self::once())
            ->method('findOneByOrderId')
            ->with('123', '444')
            ->willReturn($this->payment);

        $this->order
            ->expects(self::once())
            ->method('getState')
            ->willReturn(OrderInterface::STATE_FULFILLED);

        $this->payment->expects(self::never())->method('setMethod');

        self::expectException(PaymentMethodCannotBeChangedException::class);
        self::expectExceptionMessage('Payment method can not be changed');

        $this->paymentMethodChanger->changePaymentMethod(
            'CASH_ON_DELIVERY_METHOD',
            '123',
            $this->order,
        );
    }

    public function testThrowsAnExceptionIfPaymentMethodWithGivenCodeHasNotBeenFound(): void
    {
        $this->paymentMethodRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => 'CASH_ON_DELIVERY_METHOD'])
            ->willReturn(null);

        $this->order->expects(self::never())->method('getId');

        $this->paymentRepository->expects(self::never())->method('findOneByOrderId');

        self::expectException(\InvalidArgumentException::class);

        $this->paymentMethodChanger->changePaymentMethod(
            'CASH_ON_DELIVERY_METHOD',
            '123',
            $this->order,
        );
    }

    public function testThrowsAnExceptionIfPaymentWithGivenIdHasNotBeenFound(): void
    {
        $this->paymentMethodRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => 'CASH_ON_DELIVERY_METHOD'])
            ->willReturn($this->paymentMethod);

        $this->order->expects(self::once())->method('getId')->willReturn('444');

        $this->paymentRepository->expects(self::once())
            ->method('findOneByOrderId')
            ->with('123', '444')
            ->willReturn(null);

        $this->order->expects(self::never())->method('getState');

        self::expectException(\InvalidArgumentException::class);

        $this->paymentMethodChanger->changePaymentMethod(
            'CASH_ON_DELIVERY_METHOD',
            '123',
            $this->order,
        );
    }

    public function testChangesPaymentMethodToSpecifiedPayment(): void
    {
        $this->paymentMethodRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => 'CASH_ON_DELIVERY_METHOD'])
            ->willReturn($this->paymentMethod);

        $this->order->expects(self::once())->method('getId')->willReturn('444');

        $this->paymentRepository->expects(self::once())
            ->method('findOneByOrderId')
            ->with('123', '444')
            ->willReturn($this->payment);

        $this->order->expects(self::once())->method('getState')->willReturn(OrderInterface::STATE_NEW);

        $this->payment->expects(self::once())->method('getState')->willReturn(PaymentInterface::STATE_NEW);

        $this->payment->expects(self::once())->method('setMethod')->with($this->paymentMethod);

        self::assertSame(
            $this->order,
            $this->paymentMethodChanger->changePaymentMethod(
                'CASH_ON_DELIVERY_METHOD',
                '123',
                $this->order,
            ),
        );
    }
}
