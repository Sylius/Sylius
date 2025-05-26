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

namespace Tests\Sylius\Component\Product\Model;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Product\Model\ProductTranslation;
use Sylius\Component\Product\Model\ProductTranslationInterface;

final class ProductTranslationTest extends TestCase
{
    private ProductTranslation $productTranslation;

    protected function setUp(): void
    {
        $this->productTranslation = new ProductTranslation();
    }

    public function testImplementsSyliusProductTranslationInterface(): void
    {
        $this->assertInstanceOf(ProductTranslationInterface::class, $this->productTranslation);
    }

    public function testHasNoNameByDefault(): void
    {
        $this->assertNull($this->productTranslation->getName());
    }

    public function testItsNameIsMutable(): void
    {
        $this->productTranslation->setName('Super product');
        $this->assertSame('Super product', $this->productTranslation->getName());
    }

    public function testHasNoSlugByDefault(): void
    {
        $this->assertNull($this->productTranslation->getSlug());
    }

    public function testItsSlugIsMutable(): void
    {
        $this->productTranslation->setSlug('super-product');
        $this->assertSame('super-product', $this->productTranslation->getSlug());
    }

    public function testHasNoDescriptionByDefault(): void
    {
        $this->assertNull($this->productTranslation->getDescription());
    }

    public function testItsDescriptionIsMutable(): void
    {
        $this->productTranslation->setDescription('This product is super cool because...');
        $this->assertSame('This product is super cool because...', $this->productTranslation->getDescription());
    }

    public function testHasNoMetaKeywordsByDefault(): void
    {
        $this->assertNull($this->productTranslation->getMetaKeywords());
    }

    public function testItsMetaKeywordsIsMutable(): void
    {
        $this->productTranslation->setMetaKeywords('foo, bar, baz');
        $this->assertSame('foo, bar, baz', $this->productTranslation->getMetaKeywords());
    }

    public function testHasNoMetaDescriptionByDefault(): void
    {
        $this->assertNull($this->productTranslation->getMetaDescription());
    }

    public function testItsMetaDescriptionIsMutable(): void
    {
        $this->productTranslation->setMetaDescription('Super product');
        $this->assertSame('Super product', $this->productTranslation->getMetaDescription());
    }
}
