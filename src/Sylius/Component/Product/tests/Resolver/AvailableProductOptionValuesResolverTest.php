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

namespace Tests\Sylius\Component\Product\Resolver;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Product\Resolver\AvailableProductOptionValuesResolver;
use Sylius\Component\Product\Resolver\AvailableProductOptionValuesResolverInterface;

final class AvailableProductOptionValuesResolverTest extends TestCase
{
    private const PRODUCT_CODE = 'PRODUCT_CODE';

    private const PRODUCT_OPTION_CODE = 'PRODUCT_OPTION_CODE';

    private MockObject&ProductInterface $product;

    private MockObject&ProductOptionInterface $productOption;

    private AvailableProductOptionValuesResolver $availableProductOptionValuesResolver;

    protected function setUp(): void
    {
        $this->availableProductOptionValuesResolver = new AvailableProductOptionValuesResolver();
        $this->product = $this->createMock(ProductInterface::class);
        $this->productOption = $this->createMock(ProductOptionInterface::class);
        $this->product->method('getCode')->willReturn(self::PRODUCT_CODE);
        $this->productOption->method('getCode')->willReturn(self::PRODUCT_OPTION_CODE);
    }

    public function testImplementsAvailableProductOptionsResolverInterface(): void
    {
        self::assertInstanceOf(AvailableProductOptionValuesResolverInterface::class, $this->availableProductOptionValuesResolver);
    }

    public function testThrowsIfOptionDoesNotBelongToProduct(): void
    {
        $this->product
            ->expects($this->atLeastOnce())
            ->method('hasOption')
            ->with($this->productOption)
            ->willReturn(false)
        ;

        $this->expectExceptionMessage(sprintf(
            'Cannot resolve available product option values. Option "%s" does not belong to product "%s".',
            self::PRODUCT_OPTION_CODE,
            self::PRODUCT_CODE,
        ));

        $this->availableProductOptionValuesResolver->resolve($this->product, $this->productOption);
    }

    public function testFiltersOutValuesWithoutRelatedEnabledVariants(): void
    {
        $productOptionValue1 = $this->createMock(ProductOptionValueInterface::class);
        $productOptionValue2 = $this->createMock(ProductOptionValueInterface::class);
        $productVariant = $this->createMock(ProductVariantInterface::class);

        $this->product->method('hasOption')->with($this->productOption)->willReturn(true);

        $this->productOption->method('getValues')->willReturn(
            new ArrayCollection([
                $productOptionValue1,
                $productOptionValue2,
            ]),
        );

        $this->product->method('getEnabledVariants')->willReturn(
            new ArrayCollection([$productVariant]),
        );

        $productVariant->method('hasOptionValue')->willReturnMap([
            [$productOptionValue1, true],
            [$productOptionValue2, false],
        ]);

        $result = $this->availableProductOptionValuesResolver->resolve($this->product, $this->productOption);

        $this->assertCount(1, $result);
        $this->assertSame($productOptionValue1, $result[0]);
    }
}
