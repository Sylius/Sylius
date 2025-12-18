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
use Sylius\Bundle\ApiBundle\Validator\Constraints\ShopUserResetPasswordTokenNotExpired;
use Sylius\Bundle\ApiBundle\Validator\Constraints\ShopUserResetPasswordTokenNotExpiredValidator;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ShopUserResetPasswordTokenNotExpiredValidatorTest extends TestCase
{
    private MockObject&UserRepositoryInterface $userRepository;

    private ShopUserResetPasswordTokenNotExpiredValidator $shopUserResetPasswordTokenNotExpiredValidator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->shopUserResetPasswordTokenNotExpiredValidator = new ShopUserResetPasswordTokenNotExpiredValidator($this->userRepository, 'P1D');
    }

    public function testAConstraintValidator(): void
    {
        self::assertInstanceOf(ConstraintValidatorInterface::class, $this->shopUserResetPasswordTokenNotExpiredValidator);
    }

    public function testThrowsAnExceptionIfValueIsNotAString(): void
    {
        self::expectException(\InvalidArgumentException::class);
        $this->shopUserResetPasswordTokenNotExpiredValidator->validate(null, new ShopUserResetPasswordTokenNotExpired());
    }

    public function testThrowsAnExceptionIfConstraintIsNotAResetPasswordTokenNotExpiredConstraint(): void
    {
        self::expectException(\InvalidArgumentException::class);
        $this->shopUserResetPasswordTokenNotExpiredValidator->validate(
            '',
            $this->createMock(Constraint::class),
        );
    }

    public function testDoesNotAddViolationIfResetPasswordTokenDoesNotExist(): void
    {
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        $this->shopUserResetPasswordTokenNotExpiredValidator->initialize($executionContextMock);
        $this->userRepository->expects(self::once())->method('findOneBy')->with(['passwordResetToken' => 'token'])->willReturn(null);
        $executionContextMock->expects(self::never())->method('addViolation')->with('sylius.reset_password.token_expired');
        $this->shopUserResetPasswordTokenNotExpiredValidator->validate('token', new ShopUserResetPasswordTokenNotExpired());
    }

    public function testDoesNotAddViolationIfResetPasswordTokenIsNotExpired(): void
    {
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        /** @var UserInterface|MockObject $userMock */
        $userMock = $this->createMock(UserInterface::class);
        $this->shopUserResetPasswordTokenNotExpiredValidator->initialize($executionContextMock);
        $userMock->expects(self::once())->method('isPasswordRequestNonExpired')->with($this->callback(function (\DateInterval $dateInterval) {
            $this->assertSame('1', $dateInterval->format('%d'));

            return true;
        }))->willReturn(true);
        $this->userRepository->expects(self::once())->method('findOneBy')->with(['passwordResetToken' => 'token'])->willReturn($userMock);
        $executionContextMock->expects(self::never())->method('addViolation')->with('sylius.reset_password.token_expired');
        $this->shopUserResetPasswordTokenNotExpiredValidator->validate('token', new ShopUserResetPasswordTokenNotExpired());
    }

    public function testAddsViolationIfResetPasswordTokenIsExpired(): void
    {
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        /** @var UserInterface|MockObject $userMock */
        $userMock = $this->createMock(UserInterface::class);
        $this->shopUserResetPasswordTokenNotExpiredValidator->initialize($executionContextMock);
        $userMock->expects(self::once())->method('isPasswordRequestNonExpired')->with($this->callback(function (\DateInterval $dateInterval) {
            $this->assertSame('1', $dateInterval->format('%d'));

            return true;
        }))->willReturn(false);
        $this->userRepository->expects(self::once())->method('findOneBy')->with(['passwordResetToken' => 'token'])->willReturn($userMock);
        $executionContextMock->expects(self::once())->method('addViolation')->with('sylius.reset_password.token_expired');
        $this->shopUserResetPasswordTokenNotExpiredValidator->validate('token', new ShopUserResetPasswordTokenNotExpired());
    }
}
