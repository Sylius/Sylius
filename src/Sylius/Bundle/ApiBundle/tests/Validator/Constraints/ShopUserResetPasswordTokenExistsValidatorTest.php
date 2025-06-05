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
use Sylius\Bundle\ApiBundle\Validator\Constraints\ShopUserResetPasswordTokenExists;
use Sylius\Bundle\ApiBundle\Validator\Constraints\ShopUserResetPasswordTokenExistsValidator;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ShopUserResetPasswordTokenExistsValidatorTest extends TestCase
{
    private MockObject&UserRepositoryInterface $userRepository;

    private ShopUserResetPasswordTokenExistsValidator $shopUserResetPasswordTokenExistsValidator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->shopUserResetPasswordTokenExistsValidator = new ShopUserResetPasswordTokenExistsValidator($this->userRepository);
    }

    public function testAConstraintValidator(): void
    {
        self::assertInstanceOf(ConstraintValidatorInterface::class, $this->shopUserResetPasswordTokenExistsValidator);
    }

    public function testThrowsAnExceptionIfValueIsNotAString(): void
    {
        self::expectException(\InvalidArgumentException::class);
        $this->shopUserResetPasswordTokenExistsValidator->validate(null, new ShopUserResetPasswordTokenExists());
    }

    public function testThrowsAnExceptionIfConstraintIsNotAResetPasswordTokenExistsConstraint(): void
    {
        self::expectException(\InvalidArgumentException::class);
        $this->shopUserResetPasswordTokenExistsValidator->validate(
            '',
            $this->createMock(Constraint::class),
        );
    }

    public function testDoesNotAddViolationIfUserExists(): void
    {
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        /** @var UserInterface|MockObject $userMock */
        $userMock = $this->createMock(UserInterface::class);
        $this->shopUserResetPasswordTokenExistsValidator->initialize($executionContextMock);
        $this->userRepository->expects(self::once())->method('findOneBy')->with(['passwordResetToken' => 'token'])->willReturn($userMock);
        $executionContextMock->expects(self::never())->method('addViolation')->with('sylius.reset_password.invalid_token', ['%token%' => 'token']);
        $this->shopUserResetPasswordTokenExistsValidator->validate('token', new ShopUserResetPasswordTokenExists());
    }

    public function testAddsViolationIfResetPasswordTokenDoesNotExist(): void
    {
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        $this->shopUserResetPasswordTokenExistsValidator->initialize($executionContextMock);
        $this->userRepository->expects(self::once())->method('findOneBy')->with(['passwordResetToken' => 'token'])->willReturn(null);
        $executionContextMock->expects(self::once())->method('addViolation')->with('sylius.reset_password.invalid_token', ['%token%' => 'token']);
        $this->shopUserResetPasswordTokenExistsValidator->validate('token', new ShopUserResetPasswordTokenExists());
    }
}
