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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnit;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Order\Model\OrderItemUnit as BaseOrderItemUnit;
use Sylius\Component\Shipping\Model\ShipmentUnitInterface;

final class OrderItemUnitTest extends TestCase
{
    private MockObject&OrderItemInterface $orderItem;

    private MockObject&ProductVariantInterface $productVariant;

    private AdjustmentInterface&MockObject $nonNeutralTaxAdjustment;

    private AdjustmentInterface&MockObject $neutralTaxAdjustment;

    private \DateTime $date;

    private OrderItemUnit $orderItemUnit;

    protected function setUp(): void
    {
        $this->orderItem = $this->createMock(OrderItemInterface::class);
        $this->productVariant = $this->createMock(ProductVariantInterface::class);
        $this->nonNeutralTaxAdjustment = $this->createMock(AdjustmentInterface::class);
        $this->neutralTaxAdjustment = $this->createMock(AdjustmentInterface::class);
        $this->date = new \DateTime();
        $this->orderItemUnit = new OrderItemUnit($this->orderItem);
    }

    public function testShouldImplementOrderItemUnitInterface(): void
    {
        $this->assertInstanceOf(OrderItemUnitInterface::class, $this->orderItemUnit);
    }

    public function testShouldImplementInventoryUnitInterface(): void
    {
        $this->assertInstanceOf(InventoryUnitInterface::class, $this->orderItemUnit);
    }

    public function testShouldImplementShipmentUnitInterface(): void
    {
        $this->assertInstanceOf(ShipmentUnitInterface::class, $this->orderItemUnit);
    }

    public function testShouldExtendBaseOrderItemUnit(): void
    {
        $this->assertInstanceOf(BaseOrderItemUnit::class, $this->orderItemUnit);
    }

    public function testShouldShipmentBeMutable(): void
    {
        $shipment = $this->createMock(ShipmentInterface::class);

        $this->orderItemUnit->setShipment($shipment);

        $this->assertSame($shipment, $this->orderItemUnit->getShipment());
    }

    public function testShouldCreatedAtBeMutable(): void
    {
        $this->orderItemUnit->setCreatedAt($this->date);

        $this->assertSame($this->date, $this->orderItemUnit->getCreatedAt());
    }

    public function testShouldUpdatedAtBeMutable(): void
    {
        $this->orderItemUnit->setUpdatedAt($this->date);

        $this->assertSame($this->date, $this->orderItemUnit->getUpdatedAt());
    }

    public function testShouldStockableBeAnOrderItemVariant(): void
    {
        $this->orderItem->expects($this->once())->method('getVariant')->willReturn($this->productVariant);

        $this->assertSame($this->productVariant, $this->orderItemUnit->getStockable());
    }

    public function testShouldShippableBeAnOrderItemVariant(): void
    {
        $this->orderItem->expects($this->once())->method('getVariant')->willReturn($this->productVariant);

        $this->assertSame($this->productVariant, $this->orderItemUnit->getShippable());
    }

    public function testShouldReturnZeroTaxTotalWhenWhereAreNoTaxAdjustments(): void
    {
        $this->assertSame(0, $this->orderItemUnit->getTaxTotal());
    }

    public function testShouldReturnSumOfNeutralAndNonNeutralTaxAdjustmentsAsTaxTotal(): void
    {
        $this->neutralTaxAdjustment->expects($this->exactly(3))->method('isNeutral')->willReturn(true);
        $this->neutralTaxAdjustment->expects($this->once())->method('getType')->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $this->neutralTaxAdjustment->expects($this->once())->method('getAmount')->willReturn(200);
        $this->nonNeutralTaxAdjustment->expects($this->exactly(2))->method('isNeutral')->willReturn(false);
        $this->nonNeutralTaxAdjustment->expects($this->once())->method('getType')->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $this->nonNeutralTaxAdjustment->expects($this->exactly(3))->method('getAmount')->willReturn(300);

        $this->orderItem->expects($this->exactly(2))->method('recalculateUnitsTotal');
        $this->neutralTaxAdjustment->expects($this->once())->method('setAdjustable')->with($this->orderItemUnit);
        $this->nonNeutralTaxAdjustment->expects($this->once())->method('setAdjustable')->with($this->orderItemUnit);

        $this->orderItemUnit->addAdjustment($this->neutralTaxAdjustment);
        $this->orderItemUnit->addAdjustment($this->nonNeutralTaxAdjustment);

        $this->assertSame(500, $this->orderItemUnit->getTaxTotal());
    }

    public function testShouldReturnOnlySumOfNeutralAndNonNeutralTaxAdjustmentsAsTaxTotal(): void
    {
        $notTaxAdjustment = $this->createMock(AdjustmentInterface::class);
        $this->neutralTaxAdjustment->expects($this->exactly(4))->method('isNeutral')->willReturn(true);
        $this->neutralTaxAdjustment->expects($this->once())->method('getType')->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $this->neutralTaxAdjustment->expects($this->once())->method('getAmount')->willReturn(200);
        $this->nonNeutralTaxAdjustment->expects($this->exactly(3))->method('isNeutral')->willReturn(false);
        $this->nonNeutralTaxAdjustment->expects($this->once())->method('getType')->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $this->nonNeutralTaxAdjustment->expects($this->exactly(4))->method('getAmount')->willReturn(300);
        $notTaxAdjustment->expects($this->exactly(2))->method('isNeutral')->willReturn(false);
        $notTaxAdjustment->expects($this->once())->method('getType')->willReturn(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT);
        $notTaxAdjustment->expects($this->exactly(2))->method('getAmount')->willReturn(100);
        $this->orderItem->expects($this->exactly(3))->method('recalculateUnitsTotal');
        $this->neutralTaxAdjustment->expects($this->once())->method('setAdjustable')->with($this->orderItemUnit);
        $this->nonNeutralTaxAdjustment->expects($this->once())->method('setAdjustable')->with($this->orderItemUnit);
        $notTaxAdjustment->expects($this->once())->method('setAdjustable')->with($this->orderItemUnit);

        $this->orderItemUnit->addAdjustment($this->neutralTaxAdjustment);
        $this->orderItemUnit->addAdjustment($this->nonNeutralTaxAdjustment);
        $this->orderItemUnit->addAdjustment($notTaxAdjustment);

        $this->assertSame(500, $this->orderItemUnit->getTaxTotal());
    }
}
