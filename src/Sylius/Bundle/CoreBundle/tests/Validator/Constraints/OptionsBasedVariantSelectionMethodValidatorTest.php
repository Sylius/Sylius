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
use Sylius\Bundle\CoreBundle\Validator\Constraints\OptionsBasedVariantSelectionMethod;
use Sylius\Bundle\CoreBundle\Validator\Constraints\OptionsBasedVariantSelectionMethodValidator;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class OptionsBasedVariantSelectionMethodValidatorTest extends TestCase
{
    private ExecutionContextInterface&MockObject $context;

    private OptionsBasedVariantSelectionMethodValidator $validator;

    protected function setUp(): void
    {
        $this->context = $this->createMock(ExecutionContextInterface::class);

        $this->validator = new OptionsBasedVariantSelectionMethodValidator();
        $this->validator->initialize($this->context);
    }

    public function testConstraintValidator(): void
    {
        $this->assertInstanceOf(ConstraintValidator::class, $this->validator);
    }

    public function testDoesNotAddViolationIfProductUsesVariantChoiceSelectionMethod(): void
    {
        $product = $this->createMock(ProductInterface::class);

        $product->expects($this->once())->method('getVariantSelectionMethod')->willReturn(ProductInterface::VARIANT_SELECTION_CHOICE);
        $product->expects($this->never())->method('getOptions');
        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate($product, new OptionsBasedVariantSelectionMethod());
    }

    public function testDoesNotAddViolationIfProductUsesOptionsMatchingSelectionMethodAndHasOptions(): void
    {
        $product = $this->createMock(ProductInterface::class);

        $product->expects($this->once())->method('getVariantSelectionMethod')->willReturn(ProductInterface::VARIANT_SELECTION_MATCH);
        $product->expects($this->once())->method('getOptions')->willReturn(new ArrayCollection(['option']));
        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate($product, new OptionsBasedVariantSelectionMethod());
    }

    public function testAddsViolationIfProductUsesOptionsMatchingSelectionMethodWithoutOptions(): void
    {
        $constraint = new OptionsBasedVariantSelectionMethod();
        $product = $this->createMock(ProductInterface::class);
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $product->expects($this->once())->method('getVariantSelectionMethod')->willReturn(ProductInterface::VARIANT_SELECTION_MATCH);
        $product->expects($this->once())->method('getOptions')->willReturn(new ArrayCollection());
        $this->context->expects($this->once())->method('buildViolation')->with($constraint->message)->willReturn($violationBuilder);
        $violationBuilder->expects($this->once())->method('atPath')->with('variantSelectionMethod')->willReturn($violationBuilder);
        $violationBuilder->expects($this->once())->method('addViolation');

        $this->validator->validate($product, $constraint);
    }
}
