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

namespace Tests\Sylius\Bundle\ProductBundle\Validator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ProductBundle\Validator\Constraint\ProductVariantCombination;
use Sylius\Bundle\ProductBundle\Validator\ProductVariantCombinationValidator;
use Sylius\Component\Product\Checker\ProductVariantsParityCheckerInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ProductVariantCombinationValidatorTest extends TestCase
{
    private ExecutionContextInterface&MockObject $context;

    private MockObject&ProductVariantsParityCheckerInterface $variantsParityChecker;

    private ProductVariantCombinationValidator $productVariantCombinationValidator;

    protected function setUp(): void
    {
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->variantsParityChecker = $this->createMock(ProductVariantsParityCheckerInterface::class);

        $this->productVariantCombinationValidator = new ProductVariantCombinationValidator($this->variantsParityChecker);
        $this->productVariantCombinationValidator->initialize($this->context);
    }

    public function testConstraintValidator(): void
    {
        $this->assertInstanceOf(ConstraintValidator::class, $this->productVariantCombinationValidator);
    }

    public function testDoesNotAddViolationIfProductIsNull(): void
    {
        /** @var ProductInterface&MockObject $product */
        $product = $this->createMock(ProductInterface::class);
        /** @var ProductVariantInterface&MockObject $variant */
        $variant = $this->createMock(ProductVariantInterface::class);

        $constraint = new ProductVariantCombination(['message' => 'Variant with given options already exists']);

        $variant->expects($this->once())->method('getProduct')->willReturn(null);
        $product->expects($this->never())->method('hasVariants');
        $product->expects($this->never())->method('hasOptions');
        $this->variantsParityChecker->expects($this->never())->method('checkParity')->with($variant, $product);
        $this->context->expects($this->never())->method('addViolation');

        $this->productVariantCombinationValidator->validate($variant, $constraint);
    }

    public function testDoesNotAddViolationIfProductDoesNotHaveOptions(): void
    {
        /** @var ProductInterface&MockObject $product */
        $product = $this->createMock(ProductInterface::class);
        /** @var ProductVariantInterface&MockObject $variant */
        $variant = $this->createMock(ProductVariantInterface::class);

        $constraint = new ProductVariantCombination(['message' => 'Variant with given options already exists']);

        $variant->expects($this->once())->method('getProduct')->willReturn($product);
        $product->expects($this->once())->method('hasVariants')->willReturn(true);
        $product->expects($this->once())->method('hasOptions')->willReturn(false);
        $this->variantsParityChecker->expects($this->never())->method('checkParity')->with($variant, $product);
        $this->context->expects($this->never())->method('addViolation');

        $this->productVariantCombinationValidator->validate($variant, $constraint);
    }

    public function testDoesNotAddViolationIfProductDoesNotHaveVariants(): void
    {
        /** @var ProductInterface&MockObject $product */
        $product = $this->createMock(ProductInterface::class);
        /** @var ProductVariantInterface&MockObject $variant */
        $variant = $this->createMock(ProductVariantInterface::class);

        $constraint = new ProductVariantCombination(['message' => 'Variant with given options already exists']);

        $variant->expects($this->once())->method('getProduct')->willReturn($product);
        $product->expects($this->once())->method('hasVariants')->willReturn(false);
        $product->expects($this->never())->method('hasOptions');
        $this->context->expects($this->never())->method('addViolation');
        $this->variantsParityChecker->expects($this->never())->method('checkParity')->with($variant, $product);

        $this->productVariantCombinationValidator->validate($variant, $constraint);
    }

    public function testAddsViolationIfVariantWithGivenSameOptionsAlreadyExists(): void
    {
        /** @var ProductInterface&MockObject $product */
        $product = $this->createMock(ProductInterface::class);
        /** @var ProductVariantInterface&MockObject $variant */
        $variant = $this->createMock(ProductVariantInterface::class);

        $constraint = new ProductVariantCombination(['message' => 'Variant with given options already exists']);

        $variant->expects($this->once())->method('getProduct')->willReturn($product);
        $product->expects($this->once())->method('hasVariants')->willReturn(true);
        $product->expects($this->once())->method('hasOptions')->willReturn(true);
        $this->variantsParityChecker->expects($this->once())->method('checkParity')->with($variant, $product)->willReturn(true);
        $this->context->expects($this->once())->method('addViolation');

        $this->productVariantCombinationValidator->validate($variant, $constraint);
    }
}
