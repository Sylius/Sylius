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
use Sylius\Bundle\ApiBundle\Command\Checkout\CompleteOrder;
use Sylius\Bundle\ApiBundle\Command\Checkout\UpdateCart;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Validator\Constraints\UpdateCartEmailNotAllowed;
use Sylius\Bundle\ApiBundle\Validator\Constraints\UpdateCartEmailNotAllowedValidator;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class UpdateCartEmailNotAllowedValidatorTest extends TestCase
{
    private MockObject&OrderRepositoryInterface $orderRepository;

    private MockObject&UserContextInterface $userContext;

    private UpdateCartEmailNotAllowedValidator $updateCartEmailNotAllowedValidator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->userContext = $this->createMock(UserContextInterface::class);
        $this->updateCartEmailNotAllowedValidator = new UpdateCartEmailNotAllowedValidator($this->orderRepository, $this->userContext);
    }

    public function testAConstraintValidator(): void
    {
        self::assertInstanceOf(ConstraintValidatorInterface::class, $this->updateCartEmailNotAllowedValidator);
    }

    public function testThrowsAnExceptionIfValueIsNotAnInstanceOfUpdateCart(): void
    {
        self::expectException(\InvalidArgumentException::class);
        $this->updateCartEmailNotAllowedValidator->validate(new CompleteOrder('token'), new UpdateCartEmailNotAllowed());
    }

    public function testThrowsAnExceptionIfConstraintIsNotAnInstanceOfUpdateCartEmailNotAllowed(): void
    {
        self::expectException(\InvalidArgumentException::class);
        $this->updateCartEmailNotAllowedValidator->validate(
            new UpdateCart('token'),
            $this->createMock(Constraint::class),
        );
    }

    public function testThrowsAnExceptionIfOrderIsNull(): void
    {
        $command = new UpdateCart(orderTokenValue: 'token');
        $this->orderRepository->expects(self::once())->method('findOneBy')->with(['tokenValue' => 'token'])->willReturn(null);
        self::expectException(\InvalidArgumentException::class);
        $this->updateCartEmailNotAllowedValidator->validate($command, new UpdateCartEmailNotAllowed());
    }

    public function testDoesNotAddViolationIfTheCustomerOnTheOrderIsNull(): void
    {
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);

        $this->updateCartEmailNotAllowedValidator->initialize($executionContextMock);
        $command = new UpdateCart(email: 'shopuser@example.com', orderTokenValue: 'token');

        $this->orderRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'token'])
            ->willReturn($orderMock);

        $orderMock->expects(self::once())
            ->method('getCustomer')
            ->willReturn(null);

        $this->userContext->expects(self::never())
            ->method('getUser');

        $executionContextMock->expects(self::never())
            ->method('addViolation');

        $this->updateCartEmailNotAllowedValidator->validate($command, new UpdateCartEmailNotAllowed());
    }

    public function testDoesNotAddViolationIfTheEmailIsTheSameAsTheOneInTheOrder(): void
    {
        /** @var CustomerInterface|MockObject $customerMock */
        $customerMock = $this->createMock(CustomerInterface::class);
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var ShopUserInterface|MockObject $shopUserMock */
        $shopUserMock = $this->createMock(ShopUserInterface::class);

        $this->updateCartEmailNotAllowedValidator->initialize($executionContextMock);
        $command = new UpdateCart(email: 'shopuser@example.com', orderTokenValue: 'token');

        $customerMock->expects(self::once())
            ->method('getEmail')
            ->willReturn('shopuser@example.com');

        $this->orderRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'token'])
            ->willReturn($orderMock);

        $orderMock->expects(self::atLeastOnce())
            ->method('getCustomer')
            ->willReturn($customerMock);

        $this->userContext->expects(self::never())
            ->method('getUser');

        $executionContextMock->expects(self::never())
            ->method('addViolation');

        $this->updateCartEmailNotAllowedValidator->validate($command, new UpdateCartEmailNotAllowed());
    }

    public function testAddsViolationIfTheUserIsLoggedInAndTheyTryToChangeTheEmail(): void
    {
        /** @var CustomerInterface|MockObject $customerMock */
        $customerMock = $this->createMock(CustomerInterface::class);
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var ShopUserInterface|MockObject $shopUserMock */
        $shopUserMock = $this->createMock(ShopUserInterface::class);

        $this->updateCartEmailNotAllowedValidator->initialize($executionContextMock);
        $command = new UpdateCart(email: 'changed_email@example.com', orderTokenValue: 'token');

        $customerMock->expects(self::once())
            ->method('getEmail')
            ->willReturn('original_email@example.com');

        $this->orderRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'token'])
            ->willReturn($orderMock);

        $orderMock->expects(self::atLeastOnce())
            ->method('getCustomer')
            ->willReturn($customerMock);

        $this->userContext->expects(self::once())
            ->method('getUser')
            ->willReturn($shopUserMock);

        $executionContextMock->expects(self::once())
            ->method('addViolation')
            ->with('sylius.checkout.email.not_changeable');

        $this->updateCartEmailNotAllowedValidator->validate($command, new UpdateCartEmailNotAllowed());
    }

    public function testDoesNotAddViolationIfUserIsNotLoggedIn(): void
    {
        /** @var CustomerInterface|MockObject $customerMock */
        $customerMock = $this->createMock(CustomerInterface::class);
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);

        $this->updateCartEmailNotAllowedValidator->initialize($executionContextMock);
        $command = new UpdateCart(email: 'customer@example.com', orderTokenValue: 'token');

        $customerMock->expects(self::once())
            ->method('getEmail')
            ->willReturn('different@example.com');

        $this->orderRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'token'])
            ->willReturn($orderMock);

        $orderMock->expects(self::atLeastOnce())
            ->method('getCustomer')
            ->willReturn($customerMock);

        $this->userContext->expects(self::once())
            ->method('getUser')
            ->willReturn(null);

        $executionContextMock->expects(self::never())
            ->method('addViolation');

        $this->updateCartEmailNotAllowedValidator->validate($command, new UpdateCartEmailNotAllowed());
    }
}
