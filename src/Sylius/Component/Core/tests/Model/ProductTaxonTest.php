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
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxon;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Model\TaxonInterface;

final class ProductTaxonTest extends TestCase
{
    private ProductTaxon $productTaxon;

    protected function setUp(): void
    {
        $this->productTaxon = new ProductTaxon();
    }

    public function testShouldImplementProductTaxonInterface(): void
    {
        $this->assertInstanceOf(ProductTaxonInterface::class, $this->productTaxon);
    }

    public function testShouldProductBeMutable(): void
    {
        $product = $this->createMock(ProductInterface::class);

        $this->productTaxon->setProduct($product);

        $this->assertSame($product, $this->productTaxon->getProduct());
    }

    public function testShouldTaxonBeMutable(): void
    {
        $taxon = $this->createMock(TaxonInterface::class);

        $this->productTaxon->setTaxon($taxon);

        $this->assertSame($taxon, $this->productTaxon->getTaxon());
    }

    public function testShouldPositionBeMutable(): void
    {
        $this->productTaxon->setPosition(1);

        $this->assertSame(1, $this->productTaxon->getPosition());
    }
}
