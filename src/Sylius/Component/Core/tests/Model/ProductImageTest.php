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

use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\Image;
use Sylius\Component\Core\Model\ProductImage;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ProductImageTest extends TestCase
{
    private MockObject&ProductVariantInterface $productVariant;

    private ProductImage $productImage;

    protected function setUp(): void
    {
        $this->productVariant = $this->createMock(ProductVariantInterface::class);
        $this->productImage = new ProductImage();
    }

    public function testShouldImplementProductImageInterface(): void
    {
        $this->assertInstanceOf(ProductImageInterface::class, $this->productImage);
    }

    public function testShouldExtendImage(): void
    {
        $this->assertInstanceOf(Image::class, $this->productImage);
    }

    public function testShouldNotHaveIdByDefault(): void
    {
        $this->assertNull($this->productImage->getId());
    }

    public function testShouldNotHaveFileByDefault(): void
    {
        $this->assertFalse($this->productImage->hasFile());
        $this->assertNull($this->productImage->getFile());
    }

    public function testShouldFileBeMutable(): void
    {
        $file = new \SplFileInfo(__FILE__);

        $this->productImage->setFile($file);

        $this->assertSame($file, $this->productImage->getFile());
    }

    public function testShouldPathBeMutable(): void
    {
        $this->productImage->setPath(__FILE__);

        $this->assertSame(__FILE__, $this->productImage->getPath());
    }

    public function testShouldNotHaveTypeByDefault(): void
    {
        $this->assertNull($this->productImage->getType());
    }

    public function testShouldTypeBeMutable(): void
    {
        $this->productImage->setType('banner');

        $this->assertSame('banner', $this->productImage->getType());
    }

    public function testShouldNotHaveOwnerByDefault(): void
    {
        $this->assertNull($this->productImage->getOwner());
    }

    public function testShouldOwnerBeMutable(): void
    {
        $owner = new \stdClass();

        $this->productImage->setOwner($owner);

        $this->assertSame($owner, $this->productImage->getOwner());
    }

    public function testShouldInitializeProductVariantCollectionByDefault(): void
    {
        $this->assertInstanceOf(Collection::class, $this->productImage->getProductVariants());
    }

    public function testShouldNotHaveAnyProductVariantsByDefault(): void
    {
        $this->assertFalse($this->productImage->hasProductVariants());
    }

    public function testShouldAddProductVariant(): void
    {
        $this->productImage->addProductVariant($this->productVariant);

        $this->assertTrue($this->productImage->hasProductVariants());
        $this->assertTrue($this->productImage->hasProductVariant($this->productVariant));
    }

    public function testShouldRemoveProductVariant(): void
    {
        $this->productImage->addProductVariant($this->productVariant);

        $this->productImage->removeProductVariant($this->productVariant);

        $this->assertFalse($this->productImage->hasProductVariants());
        $this->assertFalse($this->productImage->hasProductVariant($this->productVariant));
    }
}
