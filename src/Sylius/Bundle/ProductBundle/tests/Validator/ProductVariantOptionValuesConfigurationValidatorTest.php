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

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ProductBundle\Validator\Constraint\ProductVariantOptionValuesConfiguration;
use Sylius\Bundle\ProductBundle\Validator\ProductVariantOptionValuesConfigurationValidator;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ProductVariantOptionValuesConfigurationValidatorTest extends TestCase
{
    private ExecutionContextInterface&MockObject $executionContext;

    private ProductVariantOptionValuesConfigurationValidator $productVariantOptionValuesConfigurationValidator;

    protected function setUp(): void
    {
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);

        $this->productVariantOptionValuesConfigurationValidator = new ProductVariantOptionValuesConfigurationValidator();
        $this->productVariantOptionValuesConfigurationValidator->initialize($this->executionContext);
    }

    public function testConstraintValidator(): void
    {
        $this->assertInstanceOf(ConstraintValidatorInterface::class, $this->productVariantOptionValuesConfigurationValidator);
    }

    public function testThrowsAnExceptionIfValueIsNotAProductVariant(): void
    {
        /** @var ExecutionContextInterface&MockObject $context */
        $context = $this->createMock(ExecutionContextInterface::class);

        $context->expects($this->never())->method('buildViolation');

        $this->expectException(\InvalidArgumentException::class);

        $this->productVariantOptionValuesConfigurationValidator->validate(new \stdClass(), new ProductVariantOptionValuesConfiguration());
    }

    public function testThrowsAnExceptionIfConstraintIsNotAProductVariantOptionValuesConfiguration(): void
    {
        /** @var ExecutionContextInterface&MockObject $context */
        $context = $this->createMock(ExecutionContextInterface::class);
        /** @var ProductVariantInterface&MockObject $variant */
        $variant = $this->createMock(ProductVariantInterface::class);
        /** @var Constraint&MockObject $constraint */
        $constraint = $this->createMock(Constraint::class);

        $context->expects($this->never())->method('buildViolation');

        $this->expectException(\InvalidArgumentException::class);

        $this->productVariantOptionValuesConfigurationValidator->validate($variant, $constraint);
    }

    public function testAddsViolationIfNotAllOptionsHaveConfiguredValuesOnTheVariant(): void
    {
        /** @var ProductVariantInterface&MockObject $variant */
        $variant = $this->createMock(ProductVariantInterface::class);
        /** @var ProductInterface&MockObject $product */
        $product = $this->createMock(ProductInterface::class);
        /** @var ProductOptionInterface&MockObject $firstOption */
        $firstOption = $this->createMock(ProductOptionInterface::class);
        /** @var ProductOptionInterface&MockObject $secondOption */
        $secondOption = $this->createMock(ProductOptionInterface::class);
        /** @var ProductOptionValueInterface&MockObject $optionValue */
        $optionValue = $this->createMock(ProductOptionValueInterface::class);

        $constraint = new ProductVariantOptionValuesConfiguration();

        $variant->expects($this->once())->method('getProduct')->willReturn($product);
        $product->expects($this->once())->method('hasOptions')->willReturn(true);
        $firstOption->expects($this->once())->method('getCode')->willReturn('SIZE');
        $secondOption->expects($this->once())->method('getCode')->willReturn('COLOUR');
        $product
            ->expects($this->once())
            ->method('getOptions')
            ->willReturn(new ArrayCollection([$firstOption, $secondOption]))
        ;
        $optionValue->expects($this->once())->method('getOptionCode')->willReturn('SIZE');
        $variant->expects($this->once())->method('getOptionValues')->willReturn(new ArrayCollection([$optionValue]));
        $this->executionContext->expects($this->once())->method('addViolation')->with($constraint->message);

        $this->productVariantOptionValuesConfigurationValidator->validate($variant, $constraint);
    }

    public function testDoesNothingIfAllOptionsHaveConfiguredValuesOnTheVariant(): void
    {
        /** @var ProductVariantInterface&MockObject $variant */
        $variant = $this->createMock(ProductVariantInterface::class);
        /** @var ProductInterface&MockObject $product */
        $product = $this->createMock(ProductInterface::class);
        /** @var ProductOptionInterface&MockObject $firstOption */
        $firstOption = $this->createMock(ProductOptionInterface::class);
        /** @var ProductOptionInterface&MockObject $secondOption */
        $secondOption = $this->createMock(ProductOptionInterface::class);
        /** @var ProductOptionValueInterface&MockObject $firstProductOptionValue */
        $firstProductOptionValue = $this->createMock(ProductOptionValueInterface::class);
        /** @var ProductOptionValueInterface&MockObject $secondProductOptionValue */
        $secondProductOptionValue = $this->createMock(ProductOptionValueInterface::class);

        $constraint = new ProductVariantOptionValuesConfiguration();

        $variant->expects($this->once())->method('getProduct')->willReturn($product);
        $product->expects($this->once())->method('hasOptions')->willReturn(true);
        $firstOption->expects($this->once())->method('getCode')->willReturn('SIZE');
        $secondOption->expects($this->once())->method('getCode')->willReturn('COLOUR');
        $product
            ->expects($this->once())
            ->method('getOptions')
            ->willReturn(new ArrayCollection([$firstOption, $secondOption]))
        ;
        $firstProductOptionValue->expects($this->once())->method('getOptionCode')->willReturn('SIZE');
        $secondProductOptionValue->expects($this->once())->method('getOptionCode')->willReturn('COLOUR');
        $variant
            ->expects($this->once())
            ->method('getOptionValues')
            ->willReturn(new ArrayCollection([$firstProductOptionValue, $secondProductOptionValue]))
        ;
        $this->executionContext->expects($this->never())->method('addViolation')->with($constraint->message);

        $this->productVariantOptionValuesConfigurationValidator->validate($variant, $constraint);
    }

    public function testDoesNothingIfVariantDoesNotHaveProduct(): void
    {
        /** @var ProductVariantInterface&MockObject $variant */
        $variant = $this->createMock(ProductVariantInterface::class);

        $constraint = new ProductVariantOptionValuesConfiguration();

        $variant->expects($this->once())->method('getProduct')->willReturn(null);
        $this->executionContext->expects($this->never())->method('addViolation')->with($constraint->message);

        $this->productVariantOptionValuesConfigurationValidator->validate($variant, $constraint);
    }

    public function testDoesNothingIfProductDoesNotHaveOptions(): void
    {
        /** @var ProductVariantInterface&MockObject $variant */
        $variant = $this->createMock(ProductVariantInterface::class);
        /** @var ProductInterface&MockObject $product */
        $product = $this->createMock(ProductInterface::class);

        $constraint = new ProductVariantOptionValuesConfiguration();

        $variant->expects($this->once())->method('getProduct')->willReturn($product);
        $product->expects($this->once())->method('hasOptions')->willReturn(false);
        $this->executionContext->expects($this->never())->method('addViolation')->with($constraint->message);

        $this->productVariantOptionValuesConfigurationValidator->validate($variant, $constraint);
    }
}
