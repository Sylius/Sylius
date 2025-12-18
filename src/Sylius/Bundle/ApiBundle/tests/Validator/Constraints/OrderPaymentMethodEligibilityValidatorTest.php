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

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Command\Checkout\CompleteOrder;
use Sylius\Bundle\ApiBundle\Command\Checkout\UpdateCart;
use Sylius\Bundle\ApiBundle\Validator\Constraints\OrderPaymentMethodEligibility;
use Sylius\Bundle\ApiBundle\Validator\Constraints\OrderPaymentMethodEligibilityValidator;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class OrderPaymentMethodEligibilityValidatorTest extends TestCase
{
    private MockObject&OrderRepositoryInterface $orderRepository;

    private OrderPaymentMethodEligibilityValidator $orderPaymentMethodEligibilityValidator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->orderPaymentMethodEligibilityValidator = new OrderPaymentMethodEligibilityValidator($this->orderRepository);
    }

    public function testAConstraintValidator(): void
    {
        self::assertInstanceOf(ConstraintValidatorInterface::class, $this->orderPaymentMethodEligibilityValidator);
    }

    public function testThrowsAnExceptionIfValueIsNotInstanceOfCompleteOrder(): void
    {
        self::expectException(\InvalidArgumentException::class);
        $this->orderPaymentMethodEligibilityValidator->validate('', new OrderPaymentMethodEligibility());
    }

    public function testThrowsAnExceptionIfConstraintDoesNotTypeOfOrderPaymentMethodEligibility(): void
    {
        /** @var Constraint|MockObject $constraintMock */
        $constraintMock = $this->createMock(Constraint::class);
        self::expectException(\InvalidArgumentException::class);
        $this->orderPaymentMethodEligibilityValidator->validate(new UpdateCart('token'), $constraintMock);
    }

    public function testThrowsAnExceptionIfOrderIsNull(): void
    {
        $constraint = new OrderPaymentMethodEligibility();
        $value = new CompleteOrder(orderTokenValue: 'token');
        $this->orderRepository->expects(self::once())->method('findOneBy')->with(['tokenValue' => 'token'])->willReturn(null);
        self::expectException(\InvalidArgumentException::class);
        $this->orderPaymentMethodEligibilityValidator->validate($value, $constraint);
    }

    public function testAddsViolationIfPaymentIsNotAvailableAnymore(): void
    {
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var PaymentInterface|MockObject $paymentMock */
        $paymentMock = $this->createMock(PaymentInterface::class);
        /** @var PaymentMethodInterface|MockObject $paymentMethodMock */
        $paymentMethodMock = $this->createMock(PaymentMethodInterface::class);
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);

        $this->orderPaymentMethodEligibilityValidator->initialize($executionContextMock);

        $constraint = new OrderPaymentMethodEligibility();
        $value = new CompleteOrder(orderTokenValue: 'token');

        $this->orderRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'token'])
            ->willReturn($orderMock);

        $orderMock
            ->expects(self::once())
            ->method('getPayments')
            ->willReturn(new ArrayCollection([$paymentMock]));

        $paymentMock
            ->expects($this->exactly(2))
            ->method('getMethod')
            ->willReturn($paymentMethodMock);

        $paymentMethodMock
            ->expects(self::once())
            ->method('isEnabled')
            ->willReturn(false);

        $paymentMethodMock
            ->expects(self::once())
            ->method('getName')
            ->willReturn('bank transfer');

        $executionContextMock
            ->expects(self::once())
            ->method('addViolation')
            ->with(
                'sylius.order.payment_method_eligibility',
                ['%paymentMethodName%' => 'bank transfer'],
            );

        $this->orderPaymentMethodEligibilityValidator->validate($value, $constraint);
    }

    public function testDoesNotAddViolationIfPaymentIsAvailable(): void
    {
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var PaymentInterface|MockObject $paymentMock */
        $paymentMock = $this->createMock(PaymentInterface::class);
        /** @var PaymentMethodInterface|MockObject $paymentMethodMock */
        $paymentMethodMock = $this->createMock(PaymentMethodInterface::class);
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);

        $this->orderPaymentMethodEligibilityValidator->initialize($executionContextMock);

        $constraint = new OrderPaymentMethodEligibility();
        $value = new CompleteOrder(orderTokenValue: 'token');

        $this->orderRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'token'])
            ->willReturn($orderMock);

        $orderMock
            ->expects(self::once())
            ->method('getPayments')
            ->willReturn(new ArrayCollection([$paymentMock]));

        $paymentMock
            ->expects(self::once())
            ->method('getMethod')
            ->willReturn($paymentMethodMock);

        $paymentMethodMock
            ->expects(self::once())
            ->method('isEnabled')
            ->willReturn(true);

        $executionContextMock
            ->expects(self::never())
            ->method('addViolation');

        $this->orderPaymentMethodEligibilityValidator->validate($value, $constraint);
    }
}
