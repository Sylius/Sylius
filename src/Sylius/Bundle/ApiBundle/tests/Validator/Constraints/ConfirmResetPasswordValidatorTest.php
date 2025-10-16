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
use Sylius\Bundle\ApiBundle\Command\Account\ResetPassword;
use Sylius\Bundle\ApiBundle\Validator\Constraints\ConfirmResetPassword;
use Sylius\Bundle\ApiBundle\Validator\Constraints\ConfirmResetPasswordValidator;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class ConfirmResetPasswordValidatorTest extends TestCase
{
    private ConfirmResetPasswordValidator $confirmResetPasswordValidator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->confirmResetPasswordValidator = new ConfirmResetPasswordValidator();
    }

    public function testAConstraintValidator(): void
    {
        self::assertInstanceOf(ConstraintValidatorInterface::class, $this->confirmResetPasswordValidator);
    }

    public function testDoesNotAddViolationIfPasswordsAreSame(): void
    {
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        $constraint = new ConfirmResetPassword();
        $this->confirmResetPasswordValidator->initialize($executionContextMock);
        $value = new ResetPassword('token', 'password', 'password');
        $executionContextMock->expects(self::never())->method('buildViolation');
        $this->confirmResetPasswordValidator->validate($value, $constraint);
    }

    public function testAddsViolationIfPasswordsAreDifferent(): void
    {
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        /** @var ConstraintViolationBuilderInterface|MockObject $constraintViolationBuilderMock */
        $constraintViolationBuilderMock = $this->createMock(ConstraintViolationBuilderInterface::class);
        $constraint = new ConfirmResetPassword();
        $constraint->message = 'message';
        $this->confirmResetPasswordValidator->initialize($executionContextMock);
        $value = new ResetPassword('token', 'password', 'differentPassword');
        $executionContextMock->expects(self::once())->method('buildViolation')->with($constraint->message)->willReturn($constraintViolationBuilderMock);
        $constraintViolationBuilderMock->expects(self::once())->method('atPath')->with('newPassword')->willReturn($constraintViolationBuilderMock);
        $constraintViolationBuilderMock->expects(self::once())->method('addViolation');
        $this->confirmResetPasswordValidator->validate($value, $constraint);
    }
}
