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

namespace Tests\Sylius\Component\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ImagesAwareInterface;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Product\Model\Product as BaseProduct;

final class ProductTest extends TestCase
{
    private MockObject&ProductVariantInterface $productVariant;

    private MockObject&ProductTaxonInterface $productTaxon;

    private MockObject&TaxonInterface $taxon;

    private ImageInterface&MockObject $image;

    private Product $product;

    protected function setUp(): void
    {
        $this->product = new Product();
        $this->productVariant = $this->createMock(ProductVariantInterface::class);
        $this->productVariant->expects($this->once())->method('setProduct')->with($this->product);
        $this->productTaxon = $this->createMock(ProductTaxonInterface::class);
        $this->taxon = $this->createMock(TaxonInterface::class);
        $this->image = $this->createMock(ImageInterface::class);
        $this->product->addVariant($this->productVariant);
    }

    public function testShouldImplementProductInterface(): void
    {
        $this->assertInstanceOf(ProductInterface::class, $this->product);
    }

    public function testShouldImplementImagesAwareInterface(): void
    {
        $this->assertInstanceOf(ImagesAwareInterface::class, $this->product);
    }

    public function testShouldExtendBaseProductModel(): void
    {
        $this->assertInstanceOf(BaseProduct::class, $this->product);
    }

    public function testShouldInitializeProductTaxonCollectionByDefault(): void
    {
        $this->assertInstanceOf(Collection::class, $this->product->getProductTaxons());
    }

    public function testShouldAddProductTaxons(): void
    {
        $this->product->addProductTaxon($this->productTaxon);

        $this->assertTrue($this->product->hasProductTaxon($this->productTaxon));
    }

    public function testShouldRemoveProductTaxon(): void
    {
        $this->product->addProductTaxon($this->productTaxon);

        $this->product->removeProductTaxon($this->productTaxon);

        $this->assertFalse($this->product->hasProductTaxon($this->productTaxon));
    }

    public function testShouldVariantSelectionMethodBeChoiceByDefault(): void
    {
        $this->assertSame(
            Product::VARIANT_SELECTION_CHOICE,
            $this->product->getVariantSelectionMethod(),
        );
    }

    public function testShouldVariantSelectionMethodChangeToOptionMatch(): void
    {
        $this->product->setVariantSelectionMethod(Product::VARIANT_SELECTION_MATCH);

        $this->assertSame(Product::VARIANT_SELECTION_MATCH, $this->product->getVariantSelectionMethod());
    }

    public function testShouldThrowExceptionIfAnyOtherValueIsGivenAsVariantSelectionMethod(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->product->setVariantSelectionMethod('foo');
    }

    public function testShouldNotHaveMainTaxonByDefault(): void
    {
        $this->assertNull($this->product->getMainTaxon());
    }

    public function testShouldMainTaxonBeMutable(): void
    {
        $this->product->setMainTaxon($this->taxon);

        $this->assertSame($this->taxon, $this->product->getMainTaxon());
    }

    public function testShouldInitializeImageCollectionByDefault(): void
    {
        $this->assertInstanceOf(Collection::class, $this->product->getImages());
    }

    public function testShouldAddImage(): void
    {
        $this->product->addImage($this->image);

        $this->assertTrue($this->product->hasImage($this->image));
        $this->assertTrue($this->product->hasImages());
    }

    public function testShouldRemoveImage(): void
    {
        $this->product->addImage($this->image);

        $this->product->removeImage($this->image);

        $this->assertFalse($this->product->hasImage($this->image));
    }

    public function testShouldReturnImagesByType(): void
    {
        $this->image->expects($this->once())->method('getType')->willReturn('thumbnail');
        $this->image->expects($this->once())->method('setOwner')->with($this->product);

        $this->product->addImage($this->image);

        $this->assertEquals(new ArrayCollection([$this->image]), $this->product->getImagesByType('thumbnail'));
    }

    public function testShouldProxyTaxonCollection(): void
    {
        $otherTaxon = $this->createMock(TaxonInterface::class);
        $this->productTaxon->expects($this->exactly(3))->method('getTaxon')->willReturn($this->taxon);
        $this->productTaxon->expects($this->once())->method('setProduct')->with($this->product);

        $this->product->addProductTaxon($this->productTaxon);

        $this->assertEquals([$this->taxon], $this->product->getTaxons()->toArray());
        $this->assertTrue($this->product->hasTaxon($this->taxon));
        $this->assertFalse($this->product->hasTaxon($otherTaxon));
    }
}
