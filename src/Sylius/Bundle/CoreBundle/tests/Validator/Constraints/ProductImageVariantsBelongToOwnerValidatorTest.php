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
use Sylius\Bundle\CoreBundle\Validator\Constraints\ProductImageVariantsBelongToOwner;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ProductImageVariantsBelongToOwnerValidator;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ProductImageVariantsBelongToOwnerValidatorTest extends TestCase
{
    private ExecutionContextInterface&MockObject $context;

    private ProductImageVariantsBelongToOwnerValidator $validator;

    protected function setUp(): void
    {
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new ProductImageVariantsBelongToOwnerValidator();
        $this->validator->initialize($this->context);
    }

    public function testItIsAConstraintValidator(): void
    {
        $this->assertInstanceOf(ConstraintValidatorInterface::class, $this->validator);
    }

    public function testItThrowsExceptionIfValueIsNotProductImage(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->validator->validate(new \stdClass(), new ProductImageVariantsBelongToOwner());
    }

    public function testItThrowsExceptionIfConstraintIsNotCorrectType(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $image = $this->createMock(ProductImageInterface::class);
        $this->validator->validate($image, $this->createMock(Constraint::class));
    }

    public function testItAddsViolationIfAnyVariantDoesNotBelongToProductOwner(): void
    {
        $constraint = new ProductImageVariantsBelongToOwner();

        $image = $this->createMock(ProductImageInterface::class);
        $product = $this->createMock(ProductInterface::class);
        $variant = $this->createMock(ProductVariantInterface::class);

        $image->method('getOwner')->willReturn($product);
        $image->method('getProductVariants')->willReturn(new ArrayCollection([$variant]));

        $product->method('getCode')->willReturn('MUG');
        $product->method('hasVariant')->with($variant)->willReturn(false);

        $variant->method('getCode')->willReturn('GREEN_SHIRT');

        $this->context->expects($this->once())
            ->method('addViolation')
            ->with(
                $constraint->message,
                ['%productVariantCode%' => 'GREEN_SHIRT', '%ownerCode%' => 'MUG'],
            )
        ;

        $this->validator->validate($image, $constraint);
    }

    public function testItDoesNothingIfAllVariantsBelongToProductOwner(): void
    {
        $constraint = new ProductImageVariantsBelongToOwner();

        $image = $this->createMock(ProductImageInterface::class);
        $product = $this->createMock(ProductInterface::class);
        $variant1 = $this->createMock(ProductVariantInterface::class);
        $variant2 = $this->createMock(ProductVariantInterface::class);

        $image->method('getOwner')->willReturn($product);
        $image->method('getProductVariants')->willReturn(new ArrayCollection([$variant1, $variant2]));

        $product->method('getCode')->willReturn('MUG');
        $product->method('hasVariant')->willReturn(true);

        $this->context->expects($this->never())->method('addViolation');

        $this->validator->validate($image, $constraint);
    }
}
