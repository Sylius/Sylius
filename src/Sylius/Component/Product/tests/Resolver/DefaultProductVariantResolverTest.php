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

use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariant;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Product\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Product\Resolver\DefaultProductVariantResolver;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;

final class DefaultProductVariantResolverTest extends TestCase
{
    /** @var ProductVariantRepositoryInterface<ProductVariant>&MockObject */
    private MockObject $productVariantRepository;

    private DefaultProductVariantResolver $defaultProductVariantResolver;

    protected function setUp(): void
    {
        $this->productVariantRepository = $this->createMock(ProductVariantRepositoryInterface::class);
        $this->defaultProductVariantResolver = new DefaultProductVariantResolver($this->productVariantRepository);
    }

    public function testImplementsVariantResolverInterface(): void
    {
        $this->assertInstanceOf(ProductVariantResolverInterface::class, $this->defaultProductVariantResolver);
    }

    public function testReturnsFirstVariantIfProductHasNoId(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $productVariant = $this->createMock(ProductVariantInterface::class);
        $variants = $this->createMock(Collection::class);

        $product->expects($this->once())->method('getId')->willReturn(null);
        $product->expects($this->exactly(2))->method('getEnabledVariants')->willReturn($variants);
        $variants->expects($this->once())->method('isEmpty')->willReturn(false);
        $variants->expects($this->once())->method('first')->willReturn($productVariant);

        $this->assertSame($productVariant, $this->defaultProductVariantResolver->getVariant($product));
    }

    public function testReturnsNullIfFirstVariantIsNotDefinedAndProductHasNoId(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $variants = $this->createMock(Collection::class);

        $product->expects($this->once())->method('getId')->willReturn(null);
        $product->expects($this->once())->method('getEnabledVariants')->willReturn($variants);
        $variants->expects($this->once())->method('isEmpty')->willReturn(true);

        $this->assertNull($this->defaultProductVariantResolver->getVariant($product));
    }

    public function testReturnsFirstVariantIfProductVariantRepositoryIsInitialized(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $productVariant = $this->createMock(ProductVariantInterface::class);

        $product->expects($this->once())->method('getId')->willReturn(1);
        $this->productVariantRepository->expects($this->once())->method('findBy')->with([
            'product' => $product,
            'enabled' => true,
        ], [
            'position' => 'ASC',
            'id' => 'ASC',
        ], 1)->willReturn([$productVariant]);

        $this->assertSame($productVariant, $this->defaultProductVariantResolver->getVariant($product));
    }

    public function testReturnsNullIfFirstVariantIsNotDefined(): void
    {
        $product = $this->createMock(ProductInterface::class);

        $product->expects($this->once())->method('getId')->willReturn(1);
        $this->productVariantRepository->expects($this->once())->method('findBy')->with([
            'product' => $product,
            'enabled' => true,
        ], [
            'position' => 'ASC',
            'id' => 'ASC',
        ], 1)->willReturn([]);

        $this->assertNull($this->defaultProductVariantResolver->getVariant($product));
    }
}
