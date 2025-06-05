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

namespace Tests\Sylius\Bundle\CoreBundle\Validator\Constraints;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Command\ResendOrderConfirmationEmail;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ResendOrderConfirmationEmailWithValidOrderState;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ResendOrderConfirmationEmailWithValidOrderStateValidator;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class ResendOrderConfirmationEmailWithValidOrderStateValidatorTest extends TestCase
{
    private MockObject&RepositoryInterface $orderRepository;

    private ExecutionContextInterface&MockObject $context;

    private ResendOrderConfirmationEmailWithValidOrderStateValidator $validator;

    protected function setUp(): void
    {
        $this->orderRepository = $this->createMock(RepositoryInterface::class);
        $this->context = $this->createMock(ExecutionContextInterface::class);

        $this->validator = new ResendOrderConfirmationEmailWithValidOrderStateValidator(
            $this->orderRepository,
            [OrderInterface::STATE_NEW],
        );
        $this->validator->initialize($this->context);
    }

    public function testItIsAConstraintValidator(): void
    {
        $this->assertInstanceOf(ConstraintValidator::class, $this->validator);
    }

    public function testItThrowsExceptionIfConstraintIsInvalid(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate(new ResendOrderConfirmationEmail('TOKEN'), $this->createMock(Constraint::class));
    }

    public function testItThrowsExceptionIfValueIsInvalid(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate(new \stdClass(), new ResendOrderConfirmationEmailWithValidOrderState());
    }

    public function testItDoesNothingWhenOrderDoesNotExist(): void
    {
        $this->orderRepository
            ->method('findOneBy')
            ->with(['tokenValue' => 'TOKEN'])
            ->willReturn(null)
        ;

        $this->context->expects($this->never())->method('addViolation');

        $this->validator->validate(new ResendOrderConfirmationEmail('TOKEN'), new ResendOrderConfirmationEmailWithValidOrderState());
    }

    public function testItDoesNothingWhenOrderStateIsValid(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $order->method('getState')->willReturn(OrderInterface::STATE_NEW);

        $this->orderRepository
            ->method('findOneBy')
            ->with(['tokenValue' => 'TOKEN'])
            ->willReturn($order)
        ;

        $this->context->expects($this->never())->method('addViolation');

        $this->validator->validate(new ResendOrderConfirmationEmail('TOKEN'), new ResendOrderConfirmationEmailWithValidOrderState());
    }

    public function testItAddsViolationWhenOrderStateIsInvalid(): void
    {
        $constraint = new ResendOrderConfirmationEmailWithValidOrderState();
        $order = $this->createMock(OrderInterface::class);
        $order->method('getState')->willReturn(OrderInterface::STATE_FULFILLED);

        $this->orderRepository
            ->method('findOneBy')
            ->with(['tokenValue' => 'TOKEN'])
            ->willReturn($order)
        ;

        $this->context
            ->expects($this->once())
            ->method('addViolation')
            ->with($constraint->message, ['%state%' => OrderInterface::STATE_FULFILLED])
        ;

        $this->validator->validate(new ResendOrderConfirmationEmail('TOKEN'), $constraint);
    }
}
