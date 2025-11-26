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

namespace Tests\Sylius\Component\Product\Factory;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Product\Factory\ProductFactory;
use Sylius\Component\Product\Factory\ProductFactoryInterface;
use Sylius\Component\Product\Model\Product;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariant;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Resource\Factory\FactoryInterface;

final class ProductFactoryTest extends TestCase
{
    /** @var FactoryInterface<Product>&MockObject */
    private MockObject $factory;

    /** @var FactoryInterface<ProductVariant>&MockObject */
    private MockObject $variantFactory;

    /** @var ProductFactory<ProductInterface> */
    private ProductFactory $productFactory;

    private MockObject&ProductInterface $product;

    protected function setUp(): void
    {
        $this->factory = $this->createMock(FactoryInterface::class);
        $this->variantFactory = $this->createMock(FactoryInterface::class);
        $this->productFactory = new ProductFactory($this->factory, $this->variantFactory);
        $this->product = $this->createMock(ProductInterface::class);
    }

    public function testImplementsProductFactoryInterface(): void
    {
        $this->assertInstanceOf(ProductFactoryInterface::class, $this->productFactory);
    }

    public function testCreatesNewProduct(): void
    {
        $this->factory
            ->expects($this->once())
            ->method('createNew')
            ->willReturn($this->product)
        ;

        $this->assertSame($this->product, $this->productFactory->createNew());
    }

    public function testCreatesNewProductWithVariant(): void
    {
        /** @var ProductVariantInterface&MockObject $variant */
        $variant = $this->createMock(ProductVariantInterface::class);

        $this->variantFactory->expects($this->once())->method('createNew')->willReturn($variant);

        $this->factory->expects($this->once())->method('createNew')->willReturn($this->product);

        $this->product->expects($this->once())->method('addVariant')->with($variant);

        $this->assertSame($this->product, $this->productFactory->createWithVariant());
    }
}
