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
use Sylius\Component\Core\Model\OrderItem;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Resource\Model\VersionedInterface;

final class OrderItemTest extends TestCase
{
    private MockObject&OrderItemUnitInterface $firstOrderItemUnit;

    private MockObject&OrderItemUnitInterface $secondOrderItemUnit;

    private OrderItem $orderItem;

    protected function setUp(): void
    {
        $this->firstOrderItemUnit = $this->createMock(OrderItemUnitInterface::class);
        $this->secondOrderItemUnit = $this->createMock(OrderItemUnitInterface::class);
        $this->orderItem = new OrderItem();
    }

    public function testShouldImplementVersionedInterface(): void
    {
        $this->assertInstanceOf(VersionedInterface::class, $this->orderItem);
    }

    public function testShouldReturnZeroTaxTotalWhenThereAreNoUnits(): void
    {
        $this->assertSame(0, $this->orderItem->getTaxTotal());
    }

    public function testShouldReturnTaxOfAllUnit(): void
    {
        $this->firstOrderItemUnit->expects($this->once())->method('getTotal')->willReturn(1200);
        $this->firstOrderItemUnit->expects($this->once())->method('getTaxTotal')->willReturn(200);
        $this->firstOrderItemUnit->expects($this->once())->method('getOrderItem')->willReturn($this->orderItem);
        $this->secondOrderItemUnit->expects($this->once())->method('getTotal')->willReturn(1120);
        $this->secondOrderItemUnit->expects($this->once())->method('getTaxTotal')->willReturn(120);
        $this->secondOrderItemUnit->expects($this->once())->method('getOrderItem')->willReturn($this->orderItem);

        $this->orderItem->addUnit($this->firstOrderItemUnit);
        $this->orderItem->addUnit($this->secondOrderItemUnit);

        $this->assertSame(320, $this->orderItem->getTaxTotal());
    }

    public function testShouldReturnTaxOfAllUnitsAndBothNeutralAndNonNeutralTaxAdjustments(): void
    {
        $nonNeutralTaxAdjustment = $this->createMock(AdjustmentInterface::class);
        $neutralTaxAdjustment = $this->createMock(AdjustmentInterface::class);
        $this->firstOrderItemUnit->expects($this->once())->method('getTotal')->willReturn(1200);
        $this->firstOrderItemUnit->expects($this->once())->method('getTaxTotal')->willReturn(200);
        $this->firstOrderItemUnit->expects($this->once())->method('getOrderItem')->willReturn($this->orderItem);
        $this->secondOrderItemUnit->expects($this->once())->method('getTotal')->willReturn(1120);
        $this->secondOrderItemUnit->expects($this->once())->method('getTaxTotal')->willReturn(120);
        $this->secondOrderItemUnit->expects($this->once())->method('getOrderItem')->willReturn($this->orderItem);
        $neutralTaxAdjustment->expects($this->exactly(3))->method('isNeutral')->willReturn(true);
        $neutralTaxAdjustment->expects($this->once())->method('getType')->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $neutralTaxAdjustment->expects($this->once())->method('getAmount')->willReturn(200);
        $nonNeutralTaxAdjustment->expects($this->exactly(2))->method('isNeutral')->willReturn(false);
        $nonNeutralTaxAdjustment->expects($this->once())->method('getType')->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $nonNeutralTaxAdjustment->expects($this->exactly(3))->method('getAmount')->willReturn(300);
        $neutralTaxAdjustment->expects($this->once())->method('setAdjustable')->with($this->orderItem);
        $nonNeutralTaxAdjustment->expects($this->once())->method('setAdjustable')->with($this->orderItem);

        $this->orderItem->addUnit($this->firstOrderItemUnit);
        $this->orderItem->addUnit($this->secondOrderItemUnit);
        $this->orderItem->addAdjustment($neutralTaxAdjustment);
        $this->orderItem->addAdjustment($nonNeutralTaxAdjustment);

        $this->assertSame(820, $this->orderItem->getTaxTotal());
    }

    public function testShouldReturnDiscountedUnitPriceWhichIsFirstUnitPriceLoweredByUnitPromotions(): void
    {
        $this->orderItem->setUnitPrice(10000);
        $this->firstOrderItemUnit->expects($this->once())->method('getOrderItem')->willReturn($this->orderItem);
        $this->firstOrderItemUnit->expects($this->once())->method('getTotal')->willReturn(9000);
        $this->firstOrderItemUnit
            ->expects($this->once())
            ->method('getAdjustmentsTotal')
            ->with(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)
            ->willReturn(-500);

        $this->orderItem->addUnit($this->firstOrderItemUnit);

        $this->assertSame(9500, $this->orderItem->getDiscountedUnitPrice());
    }

    public function testShouldReturnUnitPriceAsDiscountedUnitPriceIfThereAreNoUnits(): void
    {
        $this->orderItem->setUnitPrice(10000);

        $this->assertSame(10000, $this->orderItem->getDiscountedUnitPrice());
    }

    public function testShouldSubtotalConsistsOfSumOfUnitsDiscountedPrice(): void
    {
        $this->orderItem->setUnitPrice(10000);
        $this->firstOrderItemUnit
            ->expects($this->once())
            ->method('getAdjustmentsTotal')
            ->with(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)
            ->willReturn(-1667);
        $this->firstOrderItemUnit->expects($this->once())->method('getTotal')->willReturn(10000);
        $this->firstOrderItemUnit->expects($this->once())->method('getOrderItem')->willReturn($this->orderItem);
        $this->secondOrderItemUnit->expects($this->once())->method('getAdjustmentsTotal')->willReturnMap([
            [AdjustmentInterface::TAX_ADJUSTMENT, 400],
            [AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT, -3333],
        ]);
        $this->secondOrderItemUnit->expects($this->once())->method('getTotal')->willReturn(10000);
        $this->secondOrderItemUnit->expects($this->once())->method('getOrderItem')->willReturn($this->orderItem);

        $this->orderItem->addUnit($this->firstOrderItemUnit);
        $this->orderItem->addUnit($this->secondOrderItemUnit);

        $this->assertSame(15000, $this->orderItem->getSubtotal());
    }

    public function testShouldNotHaveVariantByDefault(): void
    {
        $this->assertNull($this->orderItem->getVariant());
    }

    public function testShouldHaveVersionOneByDefault(): void
    {
        $this->assertSame(1, $this->orderItem->getVersion());
    }
}
