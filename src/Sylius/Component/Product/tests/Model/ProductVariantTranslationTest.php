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
use Sylius\Component\Product\Model\ProductVariantTranslation;
use Sylius\Component\Product\Model\ProductVariantTranslationInterface;

final class ProductVariantTranslationTest extends TestCase
{
    private ProductVariantTranslation $productVariantTranslation;

    protected function setUp(): void
    {
        $this->productVariantTranslation = new ProductVariantTranslation();
    }

    public function testImplementsProductVariantTranslationInterface(): void
    {
        $this->assertInstanceOf(ProductVariantTranslationInterface::class, $this->productVariantTranslation);
    }

    public function testHasNoNameByDefault(): void
    {
        $this->assertNull($this->productVariantTranslation->getName());
    }

    public function testItsNameIsMutable(): void
    {
        $this->productVariantTranslation->setName('Super variant');
        $this->assertSame('Super variant', $this->productVariantTranslation->getName());
    }
}
