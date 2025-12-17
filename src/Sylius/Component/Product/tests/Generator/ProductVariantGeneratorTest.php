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

namespace Tests\Sylius\Component\Product\Generator;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Product\Checker\ProductVariantsParityCheckerInterface;
use Sylius\Component\Product\Exception\ProductWithoutOptionsException;
use Sylius\Component\Product\Exception\ProductWithoutOptionsValuesException;
use Sylius\Component\Product\Factory\ProductVariantFactoryInterface;
use Sylius\Component\Product\Generator\ProductVariantGenerator;
use Sylius\Component\Product\Generator\ProductVariantGeneratorInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Product\Model\ProductVariant;
use Sylius\Component\Product\Model\ProductVariantInterface;

final class ProductVariantGeneratorTest extends TestCase
{
    /** @var ProductVariantFactoryInterface<ProductVariant>&MockObject */
    private MockObject $productVariantFactory;

    private MockObject&ProductVariantsParityCheckerInterface $variantsParityChecker;

    private ProductVariantGenerator $productVariantGenerator;

    private MockObject&ProductInterface $product;

    private MockObject&ProductOptionInterface $colorOption;

    private MockObject&ProductInterface $productVariable;

    private MockObject&ProductOptionValueInterface $blackColor;

    private MockObject&ProductOptionValueInterface $redColor;

    private MockObject&ProductOptionValueInterface $whiteColor;

    private MockObject&ProductVariantInterface $permutationVariant;

    protected function setUp(): void
    {
        $this->productVariantFactory = $this->createMock(ProductVariantFactoryInterface::class);
        $this->variantsParityChecker = $this->createMock(ProductVariantsParityCheckerInterface::class);
        $this->productVariantGenerator = new ProductVariantGenerator(
            $this->productVariantFactory,
            $this->variantsParityChecker,
        );
        $this->product = $this->createMock(ProductInterface::class);
        $this->colorOption = $this->createMock(ProductOptionInterface::class);
        $this->productVariable = $this->createMock(ProductInterface::class);
        $this->blackColor = $this->createMock(ProductOptionValueInterface::class);
        $this->redColor = $this->createMock(ProductOptionValueInterface::class);
        $this->whiteColor = $this->createMock(ProductOptionValueInterface::class);
        $this->permutationVariant = $this->createMock(ProductVariantInterface::class);
    }

    public function testImplementsProductVariantGeneratorInterface(): void
    {
        $this->assertInstanceOf(ProductVariantGeneratorInterface::class, $this->productVariantGenerator);
    }

    public function testThrowsAnExceptionIfProductHasNoOptions(): void
    {
        $this->product->expects($this->once())->method('hasOptions')->willReturn(false);
        $this->expectException(ProductWithoutOptionsException::class);
        $this->productVariantGenerator->generate($this->product);
    }

    public function testThrowsAnExceptionIfProductHasNoOptionsValues(): void
    {
        $this->product->expects($this->once())->method('hasOptions')->willReturn(true);
        $this->product
            ->expects($this->once())
            ->method('getOptions')
            ->willReturn(new ArrayCollection([$this->colorOption]))
        ;

        $this->colorOption
            ->expects($this->once())
            ->method('getValues')
            ->willReturn(new ArrayCollection([]))
        ;

        $this->expectException(ProductWithoutOptionsValuesException::class);

        $this->productVariantGenerator->generate($this->product);
    }

    public function testGeneratesVariantsForEveryValueOfAnObjectsSingleOption(): void
    {
        $this->productVariable->expects($this->once())->method('hasOptions')->willReturn(true);
        $this->productVariable
            ->expects($this->once())
            ->method('getOptions')
            ->willReturn(new ArrayCollection([$this->colorOption]))
        ;

        $this->colorOption
            ->expects($this->once())
            ->method('getValues')
            ->willReturn(new ArrayCollection([$this->blackColor, $this->whiteColor, $this->redColor]))
        ;

        $this->blackColor->expects($this->atLeastOnce())->method('getCode')->willReturn('black1');

        $this->whiteColor->expects($this->atLeastOnce())->method('getCode')->willReturn('white2');

        $this->redColor->expects($this->atLeastOnce())->method('getCode')->willReturn('red3');

        $this->variantsParityChecker
            ->expects($this->atLeastOnce())
            ->method('checkParity')
            ->with($this->permutationVariant, $this->productVariable)
            ->willReturn(false)
        ;

        $this->productVariantFactory
            ->expects($this->atLeastOnce())
            ->method('createForProduct')
            ->with($this->productVariable)
            ->willReturn($this->permutationVariant)
        ;

        $this->permutationVariant
            ->expects($this->atLeastOnce())
            ->method('addOptionValue')
            ->with($this->isInstanceOf(ProductOptionValueInterface::class))
        ;

        $this->productVariable
            ->expects($this->atLeastOnce())
            ->method('addVariant')
            ->with($this->permutationVariant)
        ;

        $this->productVariantGenerator->generate($this->productVariable);
    }

    public function testDoesNotGenerateVariantIfGivenVariantExists(): void
    {
        $this->productVariable->expects($this->once())->method('hasOptions')->willReturn(true);
        $this->productVariable
            ->expects($this->once())
            ->method('getOptions')
            ->willReturn(new ArrayCollection([$this->colorOption]))
        ;

        $this->colorOption->expects($this->once())->method('getValues')->willReturn(
            new ArrayCollection([$this->blackColor, $this->whiteColor, $this->redColor]),
        );

        $this->blackColor->expects($this->atLeastOnce())->method('getCode')->willReturn('black1');

        $this->whiteColor->expects($this->atLeastOnce())->method('getCode')->willReturn('white2');

        $this->redColor->expects($this->atLeastOnce())->method('getCode')->willReturn('red3');

        $this->variantsParityChecker
            ->expects($this->atLeastOnce())
            ->method('checkParity')
            ->with($this->permutationVariant, $this->productVariable)
            ->willReturn(true)
        ;

        $this->productVariantFactory
            ->expects($this->atLeastOnce())
            ->method('createForProduct')
            ->with($this->productVariable)
            ->willReturn($this->permutationVariant)
        ;

        $this->permutationVariant
            ->expects($this->atLeastOnce())
            ->method('addOptionValue')
            ->with($this->isInstanceOf(ProductOptionValueInterface::class))
        ;

        $this->productVariable
            ->expects($this->never())
            ->method('addVariant')
            ->with($this->permutationVariant)
        ;

        $this->productVariantGenerator->generate($this->productVariable);
    }

    public function testGeneratesVariantsForEveryPossiblePermutationOfAnObjectsOptionsAndOptionValues(): void
    {
        /** @var ProductOptionInterface&MockObject $sizeOption */
        $sizeOption = $this->createMock(ProductOptionInterface::class);
        /** @var ProductOptionValueInterface&MockObject $largeSize */
        $largeSize = $this->createMock(ProductOptionValueInterface::class);
        /** @var ProductOptionValueInterface&MockObject $mediumSize */
        $mediumSize = $this->createMock(ProductOptionValueInterface::class);
        /** @var ProductOptionValueInterface&MockObject $smallSize */
        $smallSize = $this->createMock(ProductOptionValueInterface::class);

        $this->productVariable->expects($this->once())->method('hasOptions')->willReturn(true);
        $this->productVariable->expects($this->once())->method('getOptions')->willReturn(
            new ArrayCollection([$this->colorOption, $sizeOption]),
        );

        $this->colorOption->expects($this->once())->method('getValues')->willReturn(
            new ArrayCollection([$this->blackColor, $this->whiteColor, $this->redColor]),
        );

        $sizeOption->expects($this->once())->method('getValues')->willReturn(
            new ArrayCollection([$smallSize, $mediumSize, $largeSize]),
        );

        $this->blackColor->expects($this->atLeastOnce())->method('getCode')->willReturn('black1');

        $this->whiteColor->expects($this->atLeastOnce())->method('getCode')->willReturn('white2');

        $this->redColor->expects($this->atLeastOnce())->method('getCode')->willReturn('red3');

        $smallSize->expects($this->atLeastOnce())->method('getCode')->willReturn('small4');

        $mediumSize->expects($this->atLeastOnce())->method('getCode')->willReturn('medium5');

        $largeSize->expects($this->atLeastOnce())->method('getCode')->willReturn('large6');

        $this->variantsParityChecker
            ->expects($this->atLeastOnce())
            ->method('checkParity')
            ->with($this->permutationVariant, $this->productVariable)
            ->willReturn(false)
        ;

        $this->productVariantFactory
            ->expects($this->atLeastOnce())
            ->method('createForProduct')
            ->with($this->productVariable)
            ->willReturn($this->permutationVariant)
        ;

        $this->permutationVariant
            ->expects($this->atLeastOnce())
            ->method('addOptionValue')
            ->with($this->isInstanceOf(ProductOptionValueInterface::class))
        ;

        $this->productVariable
            ->expects($this->atLeastOnce())
            ->method('addVariant')
            ->with($this->permutationVariant)
        ;

        $this->productVariantGenerator->generate($this->productVariable);
    }
}
