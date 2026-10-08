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

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Command\Cart\RemoveItemFromCart;
use Sylius\Bundle\ApiBundle\Command\Checkout\CompleteOrder;
use Sylius\Bundle\ApiBundle\Command\Checkout\UpdateCart;
use Sylius\Bundle\ApiBundle\Validator\Constraints\OrderNotEmpty;
use Sylius\Bundle\ApiBundle\Validator\Constraints\OrderNotEmptyValidator;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

#[AllowMockObjectsWithoutExpectations]
final class OrderNotEmptyValidatorTest extends TestCase
{
    private MockObject&OrderRepositoryInterface $orderRepository;

    private OrderNotEmptyValidator $orderNotEmptyValidator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->orderNotEmptyValidator = new OrderNotEmptyValidator($this->orderRepository);
    }

    public function testAConstraintValidator(): void
    {
        self::assertInstanceOf(ConstraintValidatorInterface::class, $this->orderNotEmptyValidator);
    }

    public function testThrowsAnExceptionIfValueIsNotAnInstanceOfCompleteOrderOrUpdateCart(): void
    {
        self::expectException(\InvalidArgumentException::class);
        $this->orderNotEmptyValidator->validate(new RemoveItemFromCart('token', 1), new OrderNotEmpty());
    }

    public function testThrowsAnExceptionIfConstraintIsNotAnInstanceOfOrderNotEmpty(): void
    {
        self::expectException(\InvalidArgumentException::class);

        $invalidConstraint = $this->createMock(Constraint::class);

        $this->orderNotEmptyValidator->validate(
            new CompleteOrder('TOKEN'),
            $invalidConstraint,
        );
    }

    public function testThrowsAnExceptionIfOrderIsNull(): void
    {
        $value = new CompleteOrder(orderTokenValue: 'token');
        $this->orderRepository->expects(self::once())->method('findOneBy')->with(['tokenValue' => 'token'])->willReturn(null);
        self::expectException(\InvalidArgumentException::class);
        $this->orderNotEmptyValidator->validate($value, new OrderNotEmpty());
    }

    public function testAddsViolationIfTheOrderHasNoItems(): void
    {
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        $this->orderNotEmptyValidator->initialize($executionContextMock);
        $value = new CompleteOrder(orderTokenValue: 'token');
        $this->orderRepository->expects(self::once())->method('findOneBy')->with(['tokenValue' => 'token'])->willReturn($orderMock);
        $orderMock->expects(self::once())->method('getItems')->willReturn(new ArrayCollection());

        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $executionContextMock->expects(self::once())->method('buildViolation')->with('sylius.order.not_empty')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->expects(self::once())->method('setCode')->with(OrderNotEmpty::ORDER_EMPTY_ERROR)->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->expects(self::once())->method('addViolation');

        $this->orderNotEmptyValidator->validate($value, new OrderNotEmpty());
    }

    public function testDoesNotAddViolationIfTheOrderHasItems(): void
    {
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var OrderItemInterface|MockObject $orderItemMock */
        $orderItemMock = $this->createMock(OrderItemInterface::class);
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        $this->orderNotEmptyValidator->initialize($executionContextMock);
        $value = new UpdateCart(orderTokenValue: 'token');
        $this->orderRepository->expects(self::once())->method('findOneBy')->with(['tokenValue' => 'token'])->willReturn($orderMock);
        $orderMock->expects(self::once())->method('getItems')->willReturn(new ArrayCollection([$orderItemMock]));
        $executionContextMock->expects(self::never())->method('buildViolation');
        $this->orderNotEmptyValidator->validate($value, new OrderNotEmpty());
    }
}
