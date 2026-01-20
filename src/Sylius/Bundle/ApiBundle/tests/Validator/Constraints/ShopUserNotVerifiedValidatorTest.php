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
use Sylius\Bundle\ApiBundle\Command\Account\RequestShopUserVerification;
use Sylius\Bundle\ApiBundle\Command\Checkout\CompleteOrder;
use Sylius\Bundle\ApiBundle\Validator\Constraints\ShopUserNotVerified;
use Sylius\Bundle\ApiBundle\Validator\Constraints\ShopUserNotVerifiedValidator;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ShopUserNotVerifiedValidatorTest extends TestCase
{
    private MockObject&UserRepositoryInterface $shopUserRepository;

    private ShopUserNotVerifiedValidator $shopUserNotVerifiedValidator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shopUserRepository = $this->createMock(UserRepositoryInterface::class);
        $this->shopUserNotVerifiedValidator = new ShopUserNotVerifiedValidator($this->shopUserRepository);
    }

    public function testAConstraintValidator(): void
    {
        self::assertInstanceOf(ConstraintValidatorInterface::class, $this->shopUserNotVerifiedValidator);
    }

    public function testThrowsAnExceptionIfValueIsNotAnInstanceOfRequestShopUserVerification(): void
    {
        self::expectException(\InvalidArgumentException::class);
        $this->shopUserNotVerifiedValidator->validate(
            new CompleteOrder('TOKEN'),
            new ShopUserNotVerified(),
        );
    }

    public function testThrowsAnExceptionIfConstraintIsNotAnInstanceOfShopUserExists(): void
    {
        /** @var Constraint|MockObject $constraintMock */
        $constraintMock = $this->createMock(Constraint::class);
        self::expectException(\InvalidArgumentException::class);
        $this->shopUserNotVerifiedValidator->validate(new RequestShopUserVerification(42, '', ''), $constraintMock);
    }

    public function testThrowsAnExceptionIfShopUserDoesNotExist(): void
    {
        $this->shopUserRepository->expects(self::once())->method('find')->with(42)->willReturn(null);
        self::expectException(\InvalidArgumentException::class);
        $this->shopUserNotVerifiedValidator->validate(new RequestShopUserVerification(42, '', ''), new ShopUserNotVerified());
    }

    public function testAddsViolationIfUserHasBeenVerified(): void
    {
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        /** @var ShopUserInterface|MockObject $shopUserMock */
        $shopUserMock = $this->createMock(ShopUserInterface::class);
        $this->shopUserNotVerifiedValidator->initialize($executionContextMock);
        $this->shopUserRepository->expects(self::once())->method('find')->with(42)->willReturn($shopUserMock);
        $shopUserMock->expects(self::once())->method('isVerified')->willReturn(true);
        $shopUserMock->expects(self::once())->method('getEmail')->willReturn('test@sylius.com');
        $executionContextMock->expects(self::once())->method('addViolation')->with('sylius.account.is_verified', ['%email%' => 'test@sylius.com'])
        ;
        $this->shopUserNotVerifiedValidator->validate(new RequestShopUserVerification(42, '', ''), new ShopUserNotVerified());
    }

    public function testDoesNotAddViolationIfShopUserExists(): void
    {
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        /** @var ShopUserInterface|MockObject $shopUserMock */
        $shopUserMock = $this->createMock(ShopUserInterface::class);
        $this->shopUserNotVerifiedValidator->initialize($executionContextMock);
        $this->shopUserRepository->expects(self::once())->method('find')->with(42)->willReturn($shopUserMock);
        $shopUserMock->expects(self::once())->method('isVerified')->willReturn(false);
        $executionContextMock->expects(self::never())->method('addViolation')->with('sylius.account.is_verified', ['%email%' => 'test@sylius.com'])
        ;
        $this->shopUserNotVerifiedValidator->validate(new RequestShopUserVerification(42, '', ''), new ShopUserNotVerified());
    }
}
