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
use Sylius\Bundle\ProductBundle\Validator\Constraint\UniqueSimpleProductCode;
use Sylius\Bundle\ProductBundle\Validator\UniqueSimpleProductCodeValidator;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Product\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class UniqueSimpleProductCodeValidatorTest extends TestCase
{
    private ExecutionContextInterface&MockObject $context;

    private MockObject&ProductVariantRepositoryInterface $productVariantRepository;

    private UniqueSimpleProductCodeValidator $uniqueSimpleProductCodeValidator;

    protected function setUp(): void
    {
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->productVariantRepository = $this->createMock(ProductVariantRepositoryInterface::class);

        $this->uniqueSimpleProductCodeValidator = new UniqueSimpleProductCodeValidator($this->productVariantRepository);
        $this->uniqueSimpleProductCodeValidator->initialize($this->context);
    }

    public function testConstraintValidator(): void
    {
        $this->assertInstanceOf(ConstraintValidator::class, $this->uniqueSimpleProductCodeValidator);
    }

    public function testDoesNotAddViolationIfProductIsConfigurable(): void
    {
        /** @var ProductInterface&MockObject $product */
        $product = $this->createMock(ProductInterface::class);

        $constraint = new UniqueSimpleProductCode(['message' => 'Simple product code has to be unique']);

        $product->expects($this->once())->method('isSimple')->willReturn(false);
        $this->context->expects($this->never())->method('buildViolation');

        $this->uniqueSimpleProductCodeValidator->validate($product, $constraint);
    }

    public function testDoesNotAddViolationIfProductIsSimpleButCodeHasNotBeenUsedAmongNeitherProductsNorProductVariants(): void
    {
        /** @var ProductInterface&MockObject $product */
        $product = $this->createMock(ProductInterface::class);

        $constraint = new UniqueSimpleProductCode(['message' => 'Simple product code has to be unique']);

        $product->expects($this->once())->method('isSimple')->willReturn(true);
        $product->expects($this->once())->method('getCode')->willReturn('AWESOME_PRODUCT');
        $this->context->expects($this->never())->method('buildViolation');
        $this->productVariantRepository->expects($this->once())->method('findOneBy')->with(['code' => 'AWESOME_PRODUCT'])->willReturn(null);

        $this->uniqueSimpleProductCodeValidator->validate($product, $constraint);
    }

    public function testDoesNotAddViolationIfProductIsSimpleCodeHasBeenUsedButForTheSameProduct(): void
    {
        /** @var ProductInterface&MockObject $product */
        $product = $this->createMock(ProductInterface::class);
        /** @var ProductVariantInterface&MockObject $existingProductVariant */
        $existingProductVariant = $this->createMock(ProductVariantInterface::class);

        $constraint = new UniqueSimpleProductCode(['message' => 'Simple product code has to be unique']);

        $product->expects($this->once())->method('isSimple')->willReturn(true);
        $product->expects($this->once())->method('getCode')->willReturn('AWESOME_PRODUCT');
        $product->expects($this->exactly(2))->method('getId')->willReturn(1);
        $this->context->expects($this->never())->method('buildViolation');
        $this->productVariantRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => 'AWESOME_PRODUCT'])
            ->willReturn($existingProductVariant)
        ;
        $existingProductVariant->expects($this->once())->method('getProduct')->willReturn($product);

        $this->uniqueSimpleProductCodeValidator->validate($product, $constraint);
    }

    public function testAddViolationIfProductIsSimpleAndCodeHasBeenUsedInOtherProductVariant(): void
    {
        /** @var ProductInterface&MockObject $product */
        $product = $this->createMock(ProductInterface::class);
        /** @var ProductInterface&MockObject $existingProduct */
        $existingProduct = $this->createMock(ProductInterface::class);
        /** @var ProductVariantInterface&MockObject $existingProductVariant */
        $existingProductVariant = $this->createMock(ProductVariantInterface::class);
        /** @var ConstraintViolationBuilderInterface&MockObject $constraintViolationBuilder */
        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $constraint = new UniqueSimpleProductCode(['message' => 'Simple product code has to be unique']);

        $product->expects($this->once())->method('isSimple')->willReturn(true);
        $product->expects($this->once())->method('getCode')->willReturn('AWESOME_PRODUCT');
        $product->expects($this->once())->method('getId')->willReturn(1);
        $this->context
            ->expects($this->once())
            ->method('buildViolation')
            ->with('Simple product code has to be unique')
            ->willReturn($constraintViolationBuilder)
        ;
        $constraintViolationBuilder->expects($this->once())->method('atPath')->with('code')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->expects($this->once())->method('addViolation');
        $this->productVariantRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => 'AWESOME_PRODUCT'])
            ->willReturn($existingProductVariant)
        ;
        $existingProductVariant->expects($this->once())->method('getProduct')->willReturn($existingProduct);
        $existingProduct->expects($this->once())->method('getId')->willReturn(2);

        $this->uniqueSimpleProductCodeValidator->validate($product, $constraint);
    }
}
