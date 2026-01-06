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

namespace Tests\Sylius\Bundle\ProductBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ProductBundle\Form\DataTransformer\ProductVariantToProductOptionsTransformer;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

final class ProductVariantToProductOptionsTransformerTest extends TestCase
{
    private MockObject&ProductInterface $product;

    private ProductVariantToProductOptionsTransformer $transformer;

    protected function setUp(): void
    {
        $this->product = $this->createMock(ProductInterface::class);

        $this->transformer = new ProductVariantToProductOptionsTransformer($this->product);
    }

    public function testImplementsDataTransformerInterface(): void
    {
        $this->assertInstanceOf(DataTransformerInterface::class, $this->transformer);
    }

    public function testTransformsNullToArray(): void
    {
        $this->assertSame([], $this->transformer->transform(null));
    }

    public function testThrowsExceptionWhenTransformingUnsupportedType(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->transformer->transform([]);
    }

    public function testTransformsVariantIntoVariantOptions(): void
    {
        $variant = $this->createMock(ProductVariantInterface::class);
        $optionValues = $this->createMock(Collection::class);

        $variant->method('getOptionValues')->willReturn($optionValues);
        $optionValues->method('toArray')->willReturn([]);

        $this->assertSame([], $this->transformer->transform($variant));
    }

    public function testReverseTransformsNullIntoNull(): void
    {
        $this->assertNull($this->transformer->reverseTransform(null));
    }

    public function testReverseTransformsEmptyStringIntoNull(): void
    {
        $this->assertNull($this->transformer->reverseTransform(''));
    }

    public function testThrowsExceptionWhenReverseTransformingUnsupportedType(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->transformer->reverseTransform(new \stdClass());
    }

    public function testThrowsExceptionWhenproductHasNoVariants(): void
    {
        $this->product->method('getVariants')->willReturn(new ArrayCollection());
        $this->product->method('getCode')->willReturn('example');

        $optionValue = $this->createMock(ProductOptionValueInterface::class);

        $this->expectException(TransformationFailedException::class);
        $this->transformer->reverseTransform([$optionValue]);
    }

    public function testReverseTransformsWhenVariantMatchesOptions(): void
    {
        $variant = $this->createMock(ProductVariantInterface::class);
        $optionValue = $this->createMock(ProductOptionValueInterface::class);

        $variant->method('hasOptionValue')->with($optionValue)->willReturn(true);

        $this->product->method('getVariants')->willReturn(new ArrayCollection([$variant]));

        $this->assertSame($variant, $this->transformer->reverseTransform([$optionValue]));
    }

    public function testThrowsExceptionWhenVariantDoesNotMatchOptions(): void
    {
        $variant = $this->createMock(ProductVariantInterface::class);
        $optionValue = $this->createMock(ProductOptionValueInterface::class);

        $variant->method('hasOptionValue')->with($optionValue)->willReturn(false);

        $this->product->method('getVariants')->willReturn(new ArrayCollection([$variant]));
        $this->product->method('getCode')->willReturn('example');

        $this->expectException(TransformationFailedException::class);
        $this->transformer->reverseTransform([$optionValue]);
    }

    public function testThrowsExceptionWhenOptionsAreMissing(): void
    {
        $variant = $this->createMock(ProductVariantInterface::class);

        $this->product->method('getVariants')->willReturn(new ArrayCollection([$variant]));
        $this->product->method('getCode')->willReturn('example');

        $this->expectException(TransformationFailedException::class);
        $this->transformer->reverseTransform([null]);
    }
}
