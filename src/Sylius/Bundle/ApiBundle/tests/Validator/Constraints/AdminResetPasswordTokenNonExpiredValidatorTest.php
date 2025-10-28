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

    private AdminUserInterface&MockObject $adminUser;

    private ExecutionContextInterface&MockObject $executionContext;

    private AdminResetPasswordTokenNonExpiredValidator $adminResetPasswordTokenNonExpiredValidator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->adminUser = $this->createMock(AdminUserInterface::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->adminResetPasswordTokenNonExpiredValidator = new AdminResetPasswordTokenNonExpiredValidator(
            $this->userRepository,
            'P5D',
        );
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
        $constraint = $this->createMock(Constraint::class);
        $value = new ResetPassword('token', 'newPassword');
        self::expectException(\InvalidArgumentException::class);
        $this->adminResetPasswordTokenNonExpiredValidator->validate($value, $constraint);
    }

    public function testDoesNothingWhenAUserForGivenTokenDoesNotExist(): void
    {
        $value = new ResetPassword('token', 'newPassword');
        $constraint = new AdminResetPasswordTokenNonExpired();
        $this->adminResetPasswordTokenNonExpiredValidator->initialize($this->executionContext);
        $this->userRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['passwordResetToken' => 'token'])
            ->willReturn(null);
        $this->executionContext->expects(self::never())->method('addViolation');
        $this->adminResetPasswordTokenNonExpiredValidator->validate($value, $constraint);
    }

    public function testDoesNothingWhenUserPasswordResetTokenIsNonExpired(): void
    {
        $value = new ResetPassword('token', 'newPassword');
        $constraint = new AdminResetPasswordTokenNonExpired();
        $this->adminResetPasswordTokenNonExpiredValidator->initialize($this->executionContext);
        $this->adminUser->expects(self::once())->method('isPasswordRequestNonExpired')->willReturn(true);
        $this->userRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['passwordResetToken' => 'token'])
            ->willReturn($this->adminUser);
        $this->executionContext->expects(self::never())->method('addViolation');
        $this->adminResetPasswordTokenNonExpiredValidator->validate($value, $constraint);
    }

    public function testAddsAViolationWhenUserPasswordResetTokenIsExpired(): void
    {
        $value = new ResetPassword('token', 'newPassword');
        $constraint = new AdminResetPasswordTokenNonExpired();
        $this->adminResetPasswordTokenNonExpiredValidator->initialize($this->executionContext);
        $this->adminUser->expects(self::once())->method('isPasswordRequestNonExpired')->willReturn(false);
        $this->userRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['passwordResetToken' => 'token'])
            ->willReturn($this->adminUser);
        $this->executionContext->expects(self::once())->method('addViolation')->with($constraint->message);
        $this->adminResetPasswordTokenNonExpiredValidator->validate($value, $constraint);
    }
}
