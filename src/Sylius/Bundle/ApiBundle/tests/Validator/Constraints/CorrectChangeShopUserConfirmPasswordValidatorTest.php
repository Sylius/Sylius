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
use Sylius\Bundle\ApiBundle\Command\Account\ChangeShopUserPassword;
use Sylius\Bundle\ApiBundle\Validator\Constraints\CorrectChangeShopUserConfirmPassword;
use Sylius\Bundle\ApiBundle\Validator\Constraints\CorrectChangeShopUserConfirmPasswordValidator;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class CorrectChangeShopUserConfirmPasswordValidatorTest extends TestCase
{
    private CorrectChangeShopUserConfirmPasswordValidator $correctChangeShopUserConfirmPasswordValidator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->correctChangeShopUserConfirmPasswordValidator = new CorrectChangeShopUserConfirmPasswordValidator();
    }

    public function testAConstraintValidator(): void
    {
        self::assertInstanceOf(ConstraintValidatorInterface::class, $this->correctChangeShopUserConfirmPasswordValidator);
    }

    public function testDoesNotAddViolationIfPasswordsAreSame(): void
    {
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        $constraint = new CorrectChangeShopUserConfirmPassword();
        $this->correctChangeShopUserConfirmPasswordValidator->initialize($executionContextMock);
        $value = new ChangeShopUserPassword(
            newPassword: 'password',
            confirmNewPassword: 'password',
            currentPassword: 'current',
            shopUserId: 1,
        );
        $executionContextMock->expects(self::never())->method('buildViolation');
        $this->correctChangeShopUserConfirmPasswordValidator->validate($value, $constraint);
    }

    public function testAddsViolationIfPasswordsAreDifferent(): void
    {
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        /** @var ConstraintViolationBuilderInterface|MockObject $constraintViolationBuilderMock */
        $constraintViolationBuilderMock = $this->createMock(ConstraintViolationBuilderInterface::class);
        $constraint = new CorrectChangeShopUserConfirmPassword();
        $constraint->message = 'message';
        $this->correctChangeShopUserConfirmPasswordValidator->initialize($executionContextMock);
        $value = new ChangeShopUserPassword(
            newPassword: 'password',
            confirmNewPassword: 'notaPassword',
            currentPassword: 'current',
            shopUserId: 1,
        );
        $executionContextMock->expects(self::once())->method('buildViolation')->with($constraint->message)->willReturn($constraintViolationBuilderMock);
        $constraintViolationBuilderMock->expects(self::once())->method('atPath')->with('newPassword')->willReturn($constraintViolationBuilderMock);
        $constraintViolationBuilderMock->expects(self::once())->method('addViolation');
        $this->correctChangeShopUserConfirmPasswordValidator->validate($value, $constraint);
    }
}
