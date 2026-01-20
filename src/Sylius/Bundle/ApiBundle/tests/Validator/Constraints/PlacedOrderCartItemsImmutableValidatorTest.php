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
use Sylius\Bundle\ApiBundle\Command\Cart\AddItemToCart;
use Sylius\Bundle\ApiBundle\Validator\Constraints\PlacedOrderCartItemsImmutable;
use Sylius\Bundle\ApiBundle\Validator\Constraints\PlacedOrderCartItemsImmutableValidator;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class PlacedOrderCartItemsImmutableValidatorTest extends TestCase
{
    private MockObject&OrderRepositoryInterface $orderRepository;

    private PlacedOrderCartItemsImmutableValidator $placedOrderCartItemsImmutableValidator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->placedOrderCartItemsImmutableValidator = new PlacedOrderCartItemsImmutableValidator($this->orderRepository);
    }

    public function testAConstraintValidator(): void
    {
        self::assertInstanceOf(ConstraintValidatorInterface::class, $this->placedOrderCartItemsImmutableValidator);
    }

    public function testThrowsAnExceptionIfValueIsNotAddItemToCartCommand(): void
    {
        self::expectException(\InvalidArgumentException::class);
        $this->placedOrderCartItemsImmutableValidator->validate(new \stdClass(), new PlacedOrderCartItemsImmutable());
    }

    public function testThrowsAnExceptionIfConstraintIsNotPlacedOrderCartItemsImmutable(): void
    {
        self::expectException(\InvalidArgumentException::class);
        $this->placedOrderCartItemsImmutableValidator->validate(new AddItemToCart(orderTokenValue: 'orderTokenValue', productVariantCode: 'productVariantCode', quantity: 1), new NotNull());
    }

    public function testAddsViolationIfOrderIsPlaced(): void
    {
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        $this->placedOrderCartItemsImmutableValidator->initialize($executionContextMock);
        $orderMock->expects(self::once())->method('getState')->willReturn(OrderInterface::STATE_NEW);
        $this->orderRepository->expects(self::once())->method('findOneWithCompletedCheckout')->with('orderTokenValue')->willReturn($orderMock);
        $executionContextMock->expects(self::once())->method('addViolation')->with('sylius.order.cart_items_immutable')
        ;
        $this->placedOrderCartItemsImmutableValidator->validate(
            new AddItemToCart(orderTokenValue: 'orderTokenValue', productVariantCode: 'productVariantCode', quantity: 1),
            new PlacedOrderCartItemsImmutable(),
        );
    }

    public function testDoesNothingIfCheckoutIsNotCompleted(): void
    {
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        $this->placedOrderCartItemsImmutableValidator->initialize($executionContextMock);
        $this->orderRepository->expects(self::once())->method('findOneWithCompletedCheckout')->with('orderTokenValue')->willReturn(null);
        $executionContextMock->expects(self::never())->method('addViolation')->with('sylius.order.cart_items_immutable')
        ;
        $this->placedOrderCartItemsImmutableValidator->validate(
            new AddItemToCart(orderTokenValue: 'orderTokenValue', productVariantCode: 'productVariantCode', quantity: 1),
            new PlacedOrderCartItemsImmutable(),
        );
    }
}
