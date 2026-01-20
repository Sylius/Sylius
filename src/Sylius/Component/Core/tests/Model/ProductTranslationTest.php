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

use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ProductTranslation;
use Sylius\Component\Core\Model\ProductTranslationInterface;
use Sylius\Component\Product\Model\ProductTranslation as BaseProductTranslation;

final class ProductTranslationTest extends TestCase
{
    private ProductTranslation $productTranslation;

    protected function setUp(): void
    {
        $this->productTranslation = new ProductTranslation();
    }

    public function testShouldImplementCoreProductInterface(): void
    {
        $this->assertInstanceOf(ProductTranslationInterface::class, $this->productTranslation);
    }

    public function testShouldExtendBaseProductTranslation(): void
    {
        $this->assertInstanceOf(BaseProductTranslation::class, $this->productTranslation);
    }

    public function testShouldNotHaveShortDescriptionByDefault(): void
    {
        $this->assertNull($this->productTranslation->getShortDescription());
    }

    public function testShouldShortDescriptionBeMutable(): void
    {
        $this->productTranslation->setShortDescription('Amazing product...');

        $this->assertSame('Amazing product...', $this->productTranslation->getShortDescription());
    }
}
