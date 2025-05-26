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
use Sylius\Component\Product\Model\ProductAssociationType;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;

final class ProductAssociationTypeTest extends TestCase
{
    private ProductAssociationType $productAssociationType;

    protected function setUp(): void
    {
        $this->productAssociationType = new ProductAssociationType();
    }

    public function testImplementsAssociationTypeInterface(): void
    {
        $this->assertInstanceOf(ProductAssociationTypeInterface::class, $this->productAssociationType);
    }
}
