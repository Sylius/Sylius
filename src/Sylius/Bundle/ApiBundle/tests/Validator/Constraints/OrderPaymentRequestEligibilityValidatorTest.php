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

namespace Tests\Sylius\Bundle\ApiBundle\Validator\Constraints;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Checker\OrderPaymentRequestEligibilityCheckerInterface;
use Sylius\Bundle\ApiBundle\Command\Payment\AddPaymentRequest;
use Sylius\Bundle\ApiBundle\Command\Payment\UpdatePaymentRequest;
use Sylius\Bundle\ApiBundle\Validator\Constraints\OrderPaymentRequestEligibility;
use Sylius\Bundle\ApiBundle\Validator\Constraints\OrderPaymentRequestEligibilityValidator;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class OrderPaymentRequestEligibilityValidatorTest extends TestCase
{
    /** @var OrderRepositoryInterface<OrderInterface>&MockObject */
    private MockObject $orderRepositoryMock;

    /** @var PaymentRequestRepositoryInterface<PaymentRequestInterface>&MockObject */
    private MockObject $paymentRequestRepositoryMock;

    private MockObject&OrderPaymentRequestEligibilityCheckerInterface $orderPaymentRequestEligibilityCheckerMock;

    private MockObject $executionContextMock;

    private OrderPaymentRequestEligibilityValidator $validator;

    protected function setUp(): void
    {
        $this->orderRepositoryMock = $this->createMock(OrderRepositoryInterface::class);
        $this->paymentRequestRepositoryMock = $this->createMock(PaymentRequestRepositoryInterface::class);
        $this->orderPaymentRequestEligibilityCheckerMock = $this->createMock(OrderPaymentRequestEligibilityCheckerInterface::class);
        $this->executionContextMock = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new OrderPaymentRequestEligibilityValidator(
            $this->orderRepositoryMock,
            $this->paymentRequestRepositoryMock,
            $this->orderPaymentRequestEligibilityCheckerMock,
        );
        $this->validator->initialize($this->executionContextMock);
    }

    public function test_it_implements_constraint_validator_interface(): void
    {
        self::assertInstanceOf(ConstraintValidatorInterface::class, $this->validator);
    }

    public function test_it_throws_an_exception_if_value_is_not_a_supported_command(): void
    {
        self::expectException(\InvalidArgumentException::class);
        $this->validator->validate('not_a_command', new OrderPaymentRequestEligibility());
    }

    public function test_it_does_nothing_for_add_payment_request_when_order_is_not_found(): void
    {
        $command = new AddPaymentRequest('ORDER_TOKEN', 1, 'PAYMENT_METHOD_CODE');

        $this->orderRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'ORDER_TOKEN'])
            ->willReturn(null);

        $this->orderPaymentRequestEligibilityCheckerMock->expects($this->never())->method('isEligible');

        $this->executionContextMock->expects($this->never())->method('addViolation');

        $this->validator->validate($command, new OrderPaymentRequestEligibility());
    }

    public function test_it_does_nothing_for_add_payment_request_when_order_is_eligible(): void
    {
        $command = new AddPaymentRequest('ORDER_TOKEN', 1, 'PAYMENT_METHOD_CODE');

        /** @var OrderInterface&MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);

        $this->orderRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'ORDER_TOKEN'])
            ->willReturn($orderMock);

        $this->orderPaymentRequestEligibilityCheckerMock
            ->expects($this->once())
            ->method('isEligible')
            ->with($orderMock)
            ->willReturn(true);

        $this->executionContextMock->expects($this->never())->method('addViolation');

        $this->validator->validate($command, new OrderPaymentRequestEligibility());
    }

    public function test_it_adds_violation_for_add_payment_request_when_order_is_not_eligible(): void
    {
        $command = new AddPaymentRequest('ORDER_TOKEN', 1, 'PAYMENT_METHOD_CODE');

        /** @var OrderInterface&MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);

        $this->orderRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'ORDER_TOKEN'])
            ->willReturn($orderMock);

        $this->orderPaymentRequestEligibilityCheckerMock
            ->expects($this->once())
            ->method('isEligible')
            ->with($orderMock)
            ->willReturn(false);

        $this->executionContextMock
            ->expects($this->once())
            ->method('addViolation')
            ->with('sylius.payment_request.invalid_order_checkout_state');

        $this->validator->validate($command, new OrderPaymentRequestEligibility());
    }

    public function test_it_does_nothing_for_update_payment_request_when_payment_request_is_not_found(): void
    {
        $command = new UpdatePaymentRequest('PAYMENT_REQUEST_HASH');

        $this->paymentRequestRepositoryMock
            ->expects($this->once())
            ->method('find')
            ->with('PAYMENT_REQUEST_HASH')
            ->willReturn(null);

        $this->orderPaymentRequestEligibilityCheckerMock->expects($this->never())->method('isEligible');

        $this->executionContextMock->expects($this->never())->method('addViolation');

        $this->validator->validate($command, new OrderPaymentRequestEligibility());
    }

    public function test_it_does_nothing_for_update_payment_request_when_order_is_not_found(): void
    {
        $command = new UpdatePaymentRequest('PAYMENT_REQUEST_HASH');

        /** @var PaymentInterface&MockObject $paymentMock */
        $paymentMock = $this->createMock(PaymentInterface::class);
        $paymentMock->method('getOrder')->willReturn(null);

        /** @var PaymentRequestInterface&MockObject $paymentRequestMock */
        $paymentRequestMock = $this->createMock(PaymentRequestInterface::class);
        $paymentRequestMock->method('getPayment')->willReturn($paymentMock);

        $this->paymentRequestRepositoryMock
            ->expects($this->once())
            ->method('find')
            ->with('PAYMENT_REQUEST_HASH')
            ->willReturn($paymentRequestMock);

        $this->orderPaymentRequestEligibilityCheckerMock->expects($this->never())->method('isEligible');

        $this->executionContextMock->expects($this->never())->method('addViolation');

        $this->validator->validate($command, new OrderPaymentRequestEligibility());
    }

    public function test_it_does_nothing_for_update_payment_request_when_order_is_eligible(): void
    {
        $command = new UpdatePaymentRequest('PAYMENT_REQUEST_HASH');

        /** @var OrderInterface&MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);

        /** @var PaymentInterface&MockObject $paymentMock */
        $paymentMock = $this->createMock(PaymentInterface::class);
        $paymentMock->method('getOrder')->willReturn($orderMock);

        /** @var PaymentRequestInterface&MockObject $paymentRequestMock */
        $paymentRequestMock = $this->createMock(PaymentRequestInterface::class);
        $paymentRequestMock->method('getPayment')->willReturn($paymentMock);

        $this->paymentRequestRepositoryMock
            ->expects($this->once())
            ->method('find')
            ->with('PAYMENT_REQUEST_HASH')
            ->willReturn($paymentRequestMock);

        $this->orderPaymentRequestEligibilityCheckerMock
            ->expects($this->once())
            ->method('isEligible')
            ->with($orderMock)
            ->willReturn(true);

        $this->executionContextMock->expects($this->never())->method('addViolation');

        $this->validator->validate($command, new OrderPaymentRequestEligibility());
    }

    public function test_it_adds_violation_for_update_payment_request_when_order_is_not_eligible(): void
    {
        $command = new UpdatePaymentRequest('PAYMENT_REQUEST_HASH');

        /** @var OrderInterface&MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);

        /** @var PaymentInterface&MockObject $paymentMock */
        $paymentMock = $this->createMock(PaymentInterface::class);
        $paymentMock->method('getOrder')->willReturn($orderMock);

        /** @var PaymentRequestInterface&MockObject $paymentRequestMock */
        $paymentRequestMock = $this->createMock(PaymentRequestInterface::class);
        $paymentRequestMock->method('getPayment')->willReturn($paymentMock);

        $this->paymentRequestRepositoryMock
            ->expects($this->once())
            ->method('find')
            ->with('PAYMENT_REQUEST_HASH')
            ->willReturn($paymentRequestMock);

        $this->orderPaymentRequestEligibilityCheckerMock
            ->expects($this->once())
            ->method('isEligible')
            ->with($orderMock)
            ->willReturn(false);

        $this->executionContextMock
            ->expects($this->once())
            ->method('addViolation')
            ->with('sylius.payment_request.invalid_order_checkout_state');

        $this->validator->validate($command, new OrderPaymentRequestEligibility());
    }
}
