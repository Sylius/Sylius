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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Product\Model\ProductAssociation;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\Component\Product\Model\ProductAssociationType;
use Sylius\Component\Product\Model\ProductInterface;

final class ProductAssociationTest extends TestCase
{
    private ProductAssociation $productAssociation;

    private MockObject&ProductInterface $product;

    protected function setUp(): void
    {
        $this->productAssociation = new ProductAssociation();
        $this->product = $this->createMock(ProductInterface::class);
    }

    public function testImplementsProductAssociationInterface(): void
    {
        self::assertInstanceOf(ProductAssociationInterface::class, $this->productAssociation);
    }

    public function testHasOwner(): void
    {
        $this->productAssociation->setOwner($this->product);
        $this->assertSame($this->product, $this->productAssociation->getOwner());
    }

    public function testHasType(): void
    {
        /** @var ProductAssociationType&MockObject $associationType */
        $associationType = $this->createMock(ProductAssociationType::class);
        $this->productAssociation->setType($associationType);
        $this->assertSame($associationType, $this->productAssociation->getType());
    }

    public function testAddsAssociationProduct(): void
    {
        $this->productAssociation->addAssociatedProduct($this->product);
        $this->assertCount(1, $this->productAssociation->getAssociatedProducts());
    }

    public function testChecksIfProductIsAssociated(): void
    {
        $this->assertFalse($this->productAssociation->hasAssociatedProduct($this->product));
        $this->productAssociation->addAssociatedProduct($this->product);
        $this->assertTrue($this->productAssociation->hasAssociatedProduct($this->product));
        $this->productAssociation->removeAssociatedProduct($this->product);
        $this->assertFalse($this->productAssociation->hasAssociatedProduct($this->product));
    }
}
