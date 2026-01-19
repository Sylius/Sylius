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

namespace Tests\Sylius\Component\Inventory\Model;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Inventory\Model\InventoryUnit;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Inventory\Model\StockableInterface;

final class InventoryUnitTest extends TestCase
{
    private InventoryUnit $inventoryUnit;

    protected function setUp(): void
    {
        parent::setUp();
        $this->inventoryUnit = new InventoryUnit();
    }

    public function testShouldImplementInventoryUnitInterface(): void
    {
        $this->assertInstanceOf(InventoryUnitInterface::class, $this->inventoryUnit);
    }

    public function testHasNoIdByDefault(): void
    {
        $this->assertNull($this->inventoryUnit->getId());
    }

    public function testHasNoDefinedStockableSubjectByDefault(): void
    {
        $this->assertNull($this->inventoryUnit->getStockable());
    }

    public function testAllowDefiningStockableSubject(): void
    {
        $stockable = $this->createMock(StockableInterface::class);

        $this->inventoryUnit->setStockable($stockable);

        $this->assertSame($stockable, $this->inventoryUnit->getStockable());
    }
}
