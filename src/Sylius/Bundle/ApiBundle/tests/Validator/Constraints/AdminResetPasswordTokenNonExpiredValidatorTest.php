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
use Sylius\Bundle\ApiBundle\Validator\Constraints\AdminResetPasswordTokenNonExpired;
use Sylius\Bundle\ApiBundle\Validator\Constraints\AdminResetPasswordTokenNonExpiredValidator;
use Sylius\Bundle\CoreBundle\Command\Admin\Account\ResetPassword;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class AdminResetPasswordTokenNonExpiredValidatorTest extends TestCase
{
    private MockObject&UserRepositoryInterface $userRepository;

    private AdminResetPasswordTokenNonExpiredValidator $adminResetPasswordTokenNonExpiredValidator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->adminResetPasswordTokenNonExpiredValidator = new AdminResetPasswordTokenNonExpiredValidator($this->userRepository, 'P5D');
    }

    public function testAConstraintValidator(): void
    {
        self::assertInstanceOf(ConstraintValidatorInterface::class, $this->adminResetPasswordTokenNonExpiredValidator);
    }

    public function testThrowsExceptionWhenValueIsNotAResetPassword(): void
    {
        $constraint = new AdminResetPasswordTokenNonExpired();
        self::expectException(\InvalidArgumentException::class);
        $this->adminResetPasswordTokenNonExpiredValidator->validate('', $constraint);
    }

    public function testThrowsExceptionWhenConstraintIsNotAdminResetPasswordTokenNonExpired(): void
    {
        /** @var Constraint|MockObject $constraintMock */
        $constraintMock = $this->createMock(Constraint::class);
        $value = new ResetPassword('token', 'newPassword');
        self::expectException(\InvalidArgumentException::class);
        $this->adminResetPasswordTokenNonExpiredValidator->validate($value, $constraintMock);
    }

    public function testDoesNothingWhenAUserForGivenTokenDoesNotExist(): void
    {
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        $value = new ResetPassword('token', 'newPassword');
        $constraint = new AdminResetPasswordTokenNonExpired();
        $this->adminResetPasswordTokenNonExpiredValidator->initialize($executionContextMock);
        $this->userRepository->expects(self::once())->method('findOneBy')->with(['passwordResetToken' => 'token'])->willReturn(null);
        $executionContextMock->expects(self::never())->method('addViolation');
        $this->adminResetPasswordTokenNonExpiredValidator->validate($value, $constraint);
    }

    public function testDoesNothingWhenUserPasswordResetTokenIsNonExpired(): void
    {
        /** @var AdminUserInterface|MockObject $adminUserMock */
        $adminUserMock = $this->createMock(AdminUserInterface::class);
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);

        $value = new ResetPassword('token', 'newPassword');
        $constraint = new AdminResetPasswordTokenNonExpired();

        $this->adminResetPasswordTokenNonExpiredValidator->initialize($executionContextMock);

        $adminUserMock
            ->expects(self::once())
            ->method('isPasswordRequestNonExpired')
            ->willReturn(true);

        $this->userRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['passwordResetToken' => 'token'])
            ->willReturn($adminUserMock);

        $executionContextMock
            ->expects(self::never())
            ->method('addViolation');

        $this->adminResetPasswordTokenNonExpiredValidator->validate($value, $constraint);
    }

    public function testAddsAViolationWhenUserPasswordResetTokenIsExpired(): void
    {
        /** @var AdminUserInterface|MockObject $adminUserMock */
        $adminUserMock = $this->createMock(AdminUserInterface::class);
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);

        $value = new ResetPassword('token', 'newPassword');
        $constraint = new AdminResetPasswordTokenNonExpired();

        $this->adminResetPasswordTokenNonExpiredValidator->initialize($executionContextMock);

        $adminUserMock
            ->expects(self::once())
            ->method('isPasswordRequestNonExpired')
            ->willReturn(false);

        $this->userRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['passwordResetToken' => 'token'])
            ->willReturn($adminUserMock);

        $executionContextMock
            ->expects(self::once())
            ->method('addViolation')
            ->with($constraint->message);

        $this->adminResetPasswordTokenNonExpiredValidator->validate($value, $constraint);
    }
}
