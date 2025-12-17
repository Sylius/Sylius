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

namespace Tests\Sylius\Bundle\CoreBundle\Validator\Constraints;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Validator\Constraints\CartItemAvailability;
use Sylius\Bundle\CoreBundle\Validator\Constraints\CartItemAvailabilityValidator;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class CartItemAvailabilityValidatorTest extends TestCase
{
    private AvailabilityCheckerInterface&MockObject $availabilityChecker;

    private ExecutionContextInterface&MockObject $executionContext;

    private CartItemAvailabilityValidator $validator;

    protected function setUp(): void
    {
        $this->availabilityChecker = $this->createMock(AvailabilityCheckerInterface::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);

        $this->validator = new CartItemAvailabilityValidator($this->availabilityChecker);
        $this->validator->initialize($this->executionContext);
    }

    public function testIsConstraintValidator(): void
    {
        $this->assertInstanceOf(ConstraintValidator::class, $this->validator);
    }

    public function testItValidatesOnlyAddCartItemCommands(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $constraint = new CartItemAvailability();
        $this->validator->validate($this->createMock(OrderInterface::class), $constraint);
    }

    public function testItIsCartItemAvailabilityValidator(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $constraint = $this->createMock(Constraint::class);
        $command = $this->createMock(AddToCartCommandInterface::class);

        $this->validator->validate($command, $constraint);
    }

    public function testItDoesNotAddViolationIfRequestedCartItemIsAvailable(): void
    {
        $cart = $this->createMock(OrderInterface::class);
        $item = $this->createMock(OrderItemInterface::class);
        $variant = $this->createMock(ProductVariantInterface::class);
        $command = $this->createMock(AddToCartCommandInterface::class);

        $command->method('getCart')->willReturn($cart);
        $command->method('getCartItem')->willReturn($item);
        $item->method('getVariant')->willReturn($variant);
        $item->method('getQuantity')->willReturn(10);
        $cart->method('getItems')->willReturn(new ArrayCollection([]));
        $this->availabilityChecker->method('isStockSufficient')->with($variant, 10)->willReturn(true);

        $this->executionContext->expects($this->never())->method('buildViolation');

        $constraint = new CartItemAvailability();
        $this->validator->validate($command, $constraint);
    }

    public function testItAddsViolationIfRequestedCartItemIsNotAvailable(): void
    {
        $cart = $this->createMock(OrderInterface::class);
        $item = $this->createMock(OrderItemInterface::class);
        $variant = $this->createMock(ProductVariantInterface::class);
        $command = $this->createMock(AddToCartCommandInterface::class);
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $command->method('getCart')->willReturn($cart);
        $command->method('getCartItem')->willReturn($item);
        $item->method('getVariant')->willReturn($variant);
        $item->method('getQuantity')->willReturn(10);
        $cart->method('getItems')->willReturn(new ArrayCollection([]));
        $variant->method('getInventoryName')->willReturn('Mug');

        $this->availabilityChecker
            ->method('isStockSufficient')
            ->with($variant, 10)
            ->willReturn(false)
        ;

        $this->executionContext->expects($this->once())
            ->method('buildViolation')
            ->with('Insufficient stock')
            ->willReturn($violationBuilder)
        ;

        $violationBuilder->expects($this->once())->method('setParameter')->with('%itemName%', 'Mug')->willReturn($violationBuilder);
        $violationBuilder->expects($this->once())->method('atPath')->with('cartItem.quantity')->willReturn($violationBuilder);
        $violationBuilder->expects($this->once())->method('addViolation');

        $constraint = new CartItemAvailability();
        $constraint->message = 'Insufficient stock';

        $this->validator->validate($command, $constraint);
    }

    public function testItAddsViolationIfTotalQuantityExceedsAvailable(): void
    {
        $cart = $this->createMock(OrderInterface::class);
        $item = $this->createMock(OrderItemInterface::class);
        $existingItem = $this->createMock(OrderItemInterface::class);
        $variant = $this->createMock(ProductVariantInterface::class);
        $command = $this->createMock(AddToCartCommandInterface::class);
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $command->method('getCart')->willReturn($cart);
        $command->method('getCartItem')->willReturn($item);
        $item->method('getVariant')->willReturn($variant);
        $item->method('getQuantity')->willReturn(10);
        $existingItem->method('equals')->with($item)->willReturn(true);
        $existingItem->method('getQuantity')->willReturn(10);
        $variant->method('getInventoryName')->willReturn('Mug');

        $cart->method('getItems')->willReturn(new ArrayCollection([$existingItem]));
        $this->availabilityChecker->method('isStockSufficient')->with($variant, 20)->willReturn(false);

        $this->executionContext->expects($this->once())
            ->method('buildViolation')
            ->with('Insufficient stock')
            ->willReturn($violationBuilder);

        $violationBuilder->expects($this->once())->method('setParameter')->with('%itemName%', 'Mug')->willReturn($violationBuilder);
        $violationBuilder->expects($this->once())->method('atPath')->with('cartItem.quantity')->willReturn($violationBuilder);
        $violationBuilder->expects($this->once())->method('addViolation');

        $constraint = new CartItemAvailability();
        $constraint->message = 'Insufficient stock';

        $this->validator->validate($command, $constraint);
    }

    public function testItDoesNotAddViolationIfTotalQuantityIsAvailable(): void
    {
        $cart = $this->createMock(OrderInterface::class);
        $item = $this->createMock(OrderItemInterface::class);
        $existingItem = $this->createMock(OrderItemInterface::class);
        $variant = $this->createMock(ProductVariantInterface::class);
        $command = $this->createMock(AddToCartCommandInterface::class);

        $command->method('getCart')->willReturn($cart);
        $command->method('getCartItem')->willReturn($item);
        $item->method('getVariant')->willReturn($variant);
        $item->method('getQuantity')->willReturn(10);
        $existingItem->method('equals')->with($item)->willReturn(true);
        $existingItem->method('getQuantity')->willReturn(10);

        $cart->method('getItems')->willReturn(new ArrayCollection([$existingItem]));
        $this->availabilityChecker->method('isStockSufficient')->with($variant, 20)->willReturn(true);

        $this->executionContext->expects($this->never())->method('buildViolation');

        $constraint = new CartItemAvailability();
        $constraint->message = 'Insufficient stock';

        $this->validator->validate($command, $constraint);
    }
}
