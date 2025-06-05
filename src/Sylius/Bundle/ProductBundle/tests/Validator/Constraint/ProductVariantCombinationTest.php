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

namespace Tests\Sylius\Bundle\ProductBundle\Validator\Constraint;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ProductBundle\Validator\Constraint\ProductVariantCombination;
use Symfony\Component\Validator\Constraint;

final class ProductVariantCombinationTest extends TestCase
{
    private ProductVariantCombination $productVariantCombination;

    protected function setUp(): void
    {
        $this->productVariantCombination = new ProductVariantCombination();
    }

    public function testConstraintType(): void
    {
        $this->assertInstanceOf(Constraint::class, $this->productVariantCombination);
    }

    public function testClassConstraint(): void
    {
        $this->assertSame(Constraint::CLASS_CONSTRAINT, $this->productVariantCombination->getTargets());
    }
}
