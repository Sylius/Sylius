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

namespace Tests\Sylius\Bundle\InventoryBundle\Validator\Constraints;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\InventoryBundle\Validator\Constraints\InStock;
use Symfony\Component\Validator\Constraint;

final class InStockTest extends TestCase
{
    private InStock $inStock;

    protected function setUp(): void
    {
        $this->inStock = new InStock();
    }

    public function testHasValidator(): void
    {
        $this->assertSame('sylius_in_stock', $this->inStock->validatedBy());
    }

    public function testHasATarget(): void
    {
        $this->assertSame([Constraint::PROPERTY_CONSTRAINT, Constraint::CLASS_CONSTRAINT], $this->inStock->getTargets());
    }
}
