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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Product\Resolver\CompositeProductVariantResolver;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;

final class CompositeProductVariantResolverTest extends TestCase
{
    private MockObject&ProductVariantResolverInterface $firstResolver;

    private MockObject&ProductVariantResolverInterface $secondResolver;

    private CompositeProductVariantResolver $compositeProductVariantResolver;

    protected function setUp(): void
    {
        $this->firstResolver = $this->createMock(ProductVariantResolverInterface::class);
        $this->secondResolver = $this->createMock(ProductVariantResolverInterface::class);
        $this->compositeProductVariantResolver = new CompositeProductVariantResolver([
            $this->firstResolver,
            $this->secondResolver,
        ]);
    }

    public function testImplementsVariantResolverInterface(): void
    {
        $this->assertInstanceOf(ProductVariantResolverInterface::class, $this->compositeProductVariantResolver);
    }

    public function testReturnsNullWhenNoResolverReturnsAVariant(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $this->firstResolver->expects($this->once())->method('getVariant')->with($product)->willReturn(null);
        $this->secondResolver->expects($this->once())->method('getVariant')->with($product)->willReturn(null);

        $this->assertNull($this->compositeProductVariantResolver->getVariant($product));
    }

    public function testReturnsFirstResolvedVariant(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $productVariant = $this->createMock(ProductVariantInterface::class);

        $this->firstResolver->expects($this->once())->method('getVariant')->with($product)->willReturn($productVariant);
        $this->secondResolver->expects($this->never())->method('getVariant');

        $this->assertSame($productVariant, $this->compositeProductVariantResolver->getVariant($product));
    }
}
