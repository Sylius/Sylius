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
use Sylius\Bundle\CoreBundle\Validator\Constraints\CartItemQuantityRange;
use Sylius\Bundle\CoreBundle\Validator\Constraints\CartItemQuantityRangeValidator;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class CartItemQuantityRangeValidatorTest extends TestCase
{
    private MockObject&PropertyAccessorInterface $propertyAccessor;

    private ExecutionContextInterface&MockObject $executionContext;

    private CartItemQuantityRangeValidator $validator;

    protected function setUp(): void
    {
        $this->propertyAccessor = $this->createMock(PropertyAccessorInterface::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);

        $this->validator = new CartItemQuantityRangeValidator($this->propertyAccessor, 17);
        $this->validator->initialize($this->executionContext);
    }

    public function testItThrowsAnExceptionIfConstraintIsNotCartItemQuantityRange(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $invalidConstraint = $this->createMock(Constraint::class);
        $this->validator->validate(9, $invalidConstraint);
    }

    public function testItDoesNothingIfValueIsEmpty(): void
    {
        $this->executionContext->expects($this->never())->method('buildViolation');

        $constraint = new CartItemQuantityRange(min: 1);
        $this->validator->validate(null, $constraint);
    }

    public function testItDoesNothingIfValueIsInRange(): void
    {
        $this->executionContext->expects($this->never())->method('buildViolation');

        $constraint = new CartItemQuantityRange(min: 1);
        $this->validator->validate(5, $constraint);
    }

    public function testItAddsViolationIfValueIsNotInRange(): void
    {
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $violationBuilder->method('setCode')->willReturn($violationBuilder);
        $violationBuilder->method('atPath')->willReturn($violationBuilder);
        $violationBuilder->method('setParameter')->willReturn($violationBuilder);
        $violationBuilder->expects($this->once())->method('addViolation');

        $this->executionContext
            ->expects($this->once())
            ->method('buildViolation')
            ->with('sylius.cart_item.quantity.not_in_range')
            ->willReturn($violationBuilder)
        ;

        $constraint = new CartItemQuantityRange(
            notInRangeMessage: 'sylius.cart_item.quantity.not_in_range',
            min: 1,
        );

        $this->validator->validate(18, $constraint);
    }
}
