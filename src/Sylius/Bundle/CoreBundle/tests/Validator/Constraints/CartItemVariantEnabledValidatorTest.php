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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Validator\Constraints\CartItemVariantEnabled;
use Sylius\Bundle\CoreBundle\Validator\Constraints\CartItemVariantEnabledValidator;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class CartItemVariantEnabledValidatorTest extends TestCase
{
    private ExecutionContextInterface&MockObject $executionContext;

    private CartItemVariantEnabledValidator $validator;

    protected function setUp(): void
    {
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new CartItemVariantEnabledValidator();
        $this->validator->initialize($this->executionContext);
    }

    public function testItThrowsAnExceptionIfConstraintIsNotAnInstanceOfCartItemVariantEnabled(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $invalidConstraint = $this->createMock(Constraint::class);
        $this->validator->validate([], $invalidConstraint);
    }

    public function testItThrowsAnExceptionIfValueIsNotAnAddToCartCommand(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $constraint = new CartItemVariantEnabled();
        $this->validator->validate('', $constraint);
    }

    public function testItDoesNothingIfVariantIsNull(): void
    {
        $command = $this->createMock(AddToCartCommandInterface::class);
        $cartItem = $this->createMock(OrderItemInterface::class);

        $command->method('getCartItem')->willReturn($cartItem);
        $cartItem->method('getVariant')->willReturn(null);

        $this->executionContext->expects($this->never())->method('buildViolation');

        $this->validator->validate($command, new CartItemVariantEnabled());
    }

    public function testItAddsViolationIfVariantIsNotEnabled(): void
    {
        $command = $this->createMock(AddToCartCommandInterface::class);
        $cartItem = $this->createMock(OrderItemInterface::class);
        $variant = $this->createMock(ProductVariantInterface::class);
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $command->method('getCartItem')->willReturn($cartItem);
        $cartItem->method('getVariant')->willReturn($variant);
        $variant->method('isEnabled')->willReturn(false);
        $variant->method('getInventoryName')->willReturn('Mug');
        $violationBuilder->method('setParameter')->willReturn($violationBuilder);
        $violationBuilder->method('atPath')->willReturn($violationBuilder);
        $violationBuilder->expects($this->once())->method('addViolation');

        $this->executionContext
            ->expects($this->once())
            ->method('buildViolation')
            ->with('sylius.cart_item.variant.not_available')
            ->willReturn($violationBuilder)
        ;

        $this->validator->validate($command, new CartItemVariantEnabled());
    }
}
