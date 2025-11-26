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
use Sylius\Bundle\ApiBundle\Command\Account\ChangePaymentMethod;
use Sylius\Bundle\ApiBundle\Validator\Constraints\CanPaymentMethodBeChanged;
use Sylius\Bundle\ApiBundle\Validator\Constraints\CanPaymentMethodBeChangedValidator;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class CanPaymentMethodBeChangedValidatorTest extends TestCase
{
    private MockObject&OrderRepositoryInterface $orderRepository;

    private ExecutionContextInterface&MockObject $executionContext;

    private CanPaymentMethodBeChangedValidator $canPaymentMethodBeChangedValidator;

    private MockObject&OrderInterface $order;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->canPaymentMethodBeChangedValidator = new CanPaymentMethodBeChangedValidator($this->orderRepository);
        $this->canPaymentMethodBeChangedValidator->initialize($this->executionContext);
        $this->order = $this->createMock(OrderInterface::class);
    }

    public function testAConstraintValidator(): void
    {
        self::assertInstanceOf(ConstraintValidatorInterface::class, $this->canPaymentMethodBeChangedValidator);
    }

    public function testThrowsAnExceptionIfValueIsNotChangePaymentMethodCommand(): void
    {
        self::expectException(\InvalidArgumentException::class);
        $this->canPaymentMethodBeChangedValidator->validate('', new CanPaymentMethodBeChanged());
    }

    public function testThrowsAnExceptionIfConstraintIsNotAnInstanceOfCannotChangePaymentMethodForCancelledOrder(): void
    {
        /** @var Constraint|MockObject $constraint */
        $constraint = $this->createMock(Constraint::class);
        self::expectException(\InvalidArgumentException::class);
        $this->canPaymentMethodBeChangedValidator->validate(
            new ChangePaymentMethod('code', 123, 'ORDER_TOKEN'),
            $constraint,
        );
    }

    public function testThrowsAnExceptionIfOrderIsNull(): void
    {
        $command = new ChangePaymentMethod(
            orderTokenValue: 'ORDER_TOKEN',
            paymentMethodCode: 'PAYMENT_METHOD_CODE',
            paymentId: 123,
        );
        $this->orderRepository->expects(self::once())
            ->method('findOneByTokenValue')
            ->with('ORDER_TOKEN')
            ->willReturn(null);
        self::expectException(\InvalidArgumentException::class);
        $this->canPaymentMethodBeChangedValidator->validate($command, new CanPaymentMethodBeChanged());
    }

    public function testAddsViolationIfOrderIsCancelled(): void
    {
        $command = new ChangePaymentMethod(
            orderTokenValue: 'ORDER_TOKEN',
            paymentMethodCode: 'PAYMENT_METHOD_CODE',
            paymentId: 123,
        );
        $this->orderRepository->expects(self::once())
            ->method('findOneByTokenValue')
            ->with('ORDER_TOKEN')
            ->willReturn($this->order);
        $this->order->expects(self::once())->method('getState')->willReturn(OrderInterface::STATE_CANCELLED);
        $this->executionContext->expects(self::once())
            ->method('addViolation')
            ->with('sylius.payment_method.cannot_change_payment_method_for_cancelled_order');
        $this->canPaymentMethodBeChangedValidator->validate($command, new CanPaymentMethodBeChanged());
    }

    public function testDoesNothingIfOrderIsNotCancelled(): void
    {
        $command = new ChangePaymentMethod(
            orderTokenValue: 'ORDER_TOKEN',
            paymentMethodCode: 'PAYMENT_METHOD_CODE',
            paymentId: 123,
        );
        $this->orderRepository->expects(self::once())
            ->method('findOneByTokenValue')
            ->with('ORDER_TOKEN')
            ->willReturn($this->order);
        $this->order->expects(self::once())->method('getState')->willReturn(OrderInterface::STATE_NEW);
        $this->executionContext->expects(self::never())
            ->method('addViolation')
            ->with('sylius.payment_method.cannot_change_payment_method_for_cancelled_order');
        $this->canPaymentMethodBeChangedValidator->validate($command, new CanPaymentMethodBeChanged());
    }
}
