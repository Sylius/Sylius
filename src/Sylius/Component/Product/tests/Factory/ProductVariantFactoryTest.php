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
use Sylius\Component\Product\Factory\ProductVariantFactory;
use Sylius\Component\Product\Factory\ProductVariantFactoryInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariant;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Resource\Factory\FactoryInterface;

final class ProductVariantFactoryTest extends TestCase
{
    /** @var FactoryInterface<ProductVariant>&MockObject */
    private MockObject $factory;

    /** @var ProductVariantFactory<ProductVariantInterface> */
    private ProductVariantFactory $productVariantFactory;

    private MockObject&ProductVariantInterface $variant;

    protected function setUp(): void
    {
        $this->factory = $this->createMock(FactoryInterface::class);
        $this->productVariantFactory = new ProductVariantFactory($this->factory);
        $this->variant = $this->createMock(ProductVariantInterface::class);
    }

    public function testAResourceFactory(): void
    {
        $this->assertInstanceOf(FactoryInterface::class, $this->productVariantFactory);
    }

    public function testImplementsVariantFactoryInterface(): void
    {
        $this->assertInstanceOf(ProductVariantFactoryInterface::class, $this->productVariantFactory);
    }

    public function testCreatesNewVariant(): void
    {
        $this->factory->expects($this->once())->method('createNew')->willReturn($this->variant);

        $this->assertSame($this->variant, $this->productVariantFactory->createNew());
    }

    public function testCreatesAVariantAndAssignsAProductToIt(): void
    {
        /** @var ProductInterface&MockObject $product */
        $product = $this->createMock(ProductInterface::class);

        $this->factory->expects($this->once())->method('createNew')->willReturn($this->variant);

        $this->variant->expects($this->once())->method('setProduct')->with($product);

        $this->assertSame($this->variant, $this->productVariantFactory->createForProduct($product));
    }
}
