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

namespace Tests\Sylius\Component\Order\Model;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Model\OrderItemUnit;
use Sylius\Component\Order\Model\OrderItemUnitInterface;

final class OrderItemUnitTest extends TestCase
{
    private MockObject&OrderItemInterface $orderItem;

    private OrderItemUnit $orderItemUnit;

    private AdjustmentInterface&MockObject $adjustment1;

    private AdjustmentInterface&MockObject $adjustment2;

    private AdjustmentInterface&MockObject $adjustment3;

    protected function setUp(): void
    {
        $this->orderItem = $this->createMock(OrderItemInterface::class);
        $this->orderItemUnit = new OrderItemUnit($this->orderItem);
        $this->adjustment1 = $this->createMock(AdjustmentInterface::class);
        $this->adjustment2 = $this->createMock(AdjustmentInterface::class);
        $this->adjustment3 = $this->createMock(AdjustmentInterface::class);
    }

    public function testImplementsOrderItemUnitInterface(): void
    {
        $this->assertInstanceOf(OrderItemUnitInterface::class, $this->orderItemUnit);
    }

    public function testTotalWhenThereAreNoAdjustments(): void
    {
        $this->orderItem->expects($this->once())->method('getUnitPrice')->willReturn(1000);

        $this->assertSame(1000, $this->orderItemUnit->getTotal());
    }

    public function testIncludesNonNeutralAdjustmentsInTotal(): void
    {
        $this->adjustment1->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);
        $this->adjustment1->expects($this->atLeastOnce())->method('getAmount')->willReturn(400);

        $this->orderItem->expects($this->once())->method('getUnitPrice')->willReturn(1000);
        $this->orderItem->expects($this->once())->method('recalculateUnitsTotal');

        $this->adjustment1->expects($this->once())->method('setAdjustable')->with($this->orderItemUnit);

        $this->orderItemUnit->addAdjustment($this->adjustment1);

        $this->assertSame(1400, $this->orderItemUnit->getTotal());
    }

    public function testReturns0AsTotalEvenWhenAdjustmentsDecreasesItBelow0(): void
    {
        $this->adjustment1->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);
        $this->adjustment1->expects($this->atLeastOnce())->method('getAmount')->willReturn(-1400);

        $this->orderItem->expects($this->once())->method('recalculateUnitsTotal');

        $this->adjustment1->expects($this->once())->method('setAdjustable')->with($this->orderItemUnit);

        $this->orderItemUnit->addAdjustment($this->adjustment1);

        $this->assertSame(0, $this->orderItemUnit->getTotal());
    }

    public function testAddsAndRemovesAdjustments(): void
    {
        $this->orderItem->expects($this->atLeastOnce())->method('recalculateUnitsTotal');

        $this->adjustment1->expects($this->atLeastOnce())->method('isNeutral')->willReturn(true);
        $this->adjustment1->expects($this->once())->method('isLocked')->willReturn(false);

        $callIndex = 0;
        $this->adjustment1
            ->expects($this->exactly(2))
            ->method('setAdjustable')
            ->with($this->callback(function ($arg) use (&$callIndex) {
                if ($callIndex === 0) {
                    $this->assertSame($this->orderItemUnit, $arg);
                } elseif ($callIndex === 1) {
                    $this->assertNull($arg);
                }
                ++$callIndex;

                return true;
            }));

        $this->orderItemUnit->addAdjustment($this->adjustment1);
        $this->assertTrue($this->orderItemUnit->hasAdjustment($this->adjustment1));

        $this->orderItemUnit->removeAdjustment($this->adjustment1);
        $this->assertFalse($this->orderItemUnit->hasAdjustment($this->adjustment1));
    }

    public function testDoesNotRemoveAdjustmentWhenItIsLocked(): void
    {
        $this->orderItem->expects($this->once())->method('recalculateUnitsTotal');

        $this->adjustment1->expects($this->atLeastOnce())->method('isNeutral')->willReturn(true);
        $this->adjustment1->expects($this->once())->method('setAdjustable')->with($this->orderItemUnit);

        $this->orderItemUnit->addAdjustment($this->adjustment1);
        $this->assertTrue($this->orderItemUnit->hasAdjustment($this->adjustment1));

        $this->adjustment1->expects($this->once())->method('isLocked')->willReturn(true);

        $this->orderItemUnit->removeAdjustment($this->adjustment1);
        $this->assertTrue($this->orderItemUnit->hasAdjustment($this->adjustment1));
    }

    public function testHasCorrectTotalAfterAdjustmentAddAndRemove(): void
    {
        $this->orderItem->expects($this->exactly(2))->method('getUnitPrice')->willReturn(1000);
        $this->orderItem->expects($this->exactly(4))->method('recalculateUnitsTotal');

        $this->adjustment1->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);
        $this->adjustment1->expects($this->atLeastOnce())->method('isLocked')->willReturn(false);
        $this->adjustment1->expects($this->atLeastOnce())->method('getAmount')->willReturn(100);

        $callIndex = 0;
        $this->adjustment1
            ->expects($this->exactly(2))
            ->method('setAdjustable')
            ->with($this->callback(function ($arg) use (&$callIndex) {
                if ($callIndex === 0) {
                    $this->assertSame($this->orderItemUnit, $arg);
                } elseif ($callIndex === 1) {
                    $this->assertNull($arg);
                }
                ++$callIndex;

                return true;
            }));

        $this->adjustment2->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);
        $this->adjustment2->expects($this->atLeastOnce())->method('getAmount')->willReturn(50);
        $this->adjustment2->expects($this->once())->method('setAdjustable')->with($this->orderItemUnit);

        $this->adjustment3->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);
        $this->adjustment3->expects($this->atLeastOnce())->method('getAmount')->willReturn(250);
        $this->adjustment3->expects($this->once())->method('setAdjustable')->with($this->orderItemUnit);

        $this->orderItemUnit->addAdjustment($this->adjustment1);
        $this->orderItemUnit->addAdjustment($this->adjustment2);

        $this->assertSame(1000 + 100 + 50, $this->orderItemUnit->getTotal());

        $this->orderItemUnit->addAdjustment($this->adjustment3);
        $this->orderItemUnit->removeAdjustment($this->adjustment1);

        $this->assertSame(1000 + 50 + 250, $this->orderItemUnit->getTotal());
    }

    public function testHasCorrectTotalAfterNeutralAdjustmentAddAndRemove(): void
    {
        $this->orderItem->expects($this->exactly(2))->method('getUnitPrice')->willReturn(1000);
        $this->orderItem->expects($this->exactly(2))->method('recalculateUnitsTotal');

        $this->adjustment1->expects($this->atLeastOnce())->method('isNeutral')->willReturn(true);
        $this->adjustment1->expects($this->atLeastOnce())->method('isLocked')->willReturn(false);

        $callIndex = 0;
        $this->adjustment1
            ->expects($this->exactly(2))
            ->method('setAdjustable')
            ->with($this->callback(function ($arg) use (&$callIndex) {
                if ($callIndex === 0) {
                    $this->assertSame($this->orderItemUnit, $arg);
                } elseif ($callIndex === 1) {
                    $this->assertNull($arg);
                }
                ++$callIndex;

                return true;
            }));

        $this->orderItemUnit->addAdjustment($this->adjustment1);
        $this->assertSame(1000, $this->orderItemUnit->getTotal());

        $this->orderItemUnit->removeAdjustment($this->adjustment1);
        $this->assertSame(1000, $this->orderItemUnit->getTotal());
    }

    public function testHasProperTotalAfterOrderItemUnitPriceChange(): void
    {
        $this->orderItem->expects($this->exactly(2))->method('recalculateUnitsTotal');

        $this->adjustment1->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);
        $this->adjustment1->expects($this->once())->method('setAdjustable')->with($this->orderItemUnit);
        $this->adjustment1->expects($this->atLeastOnce())->method('getAmount')->willReturn(100);

        $this->adjustment2->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);
        $this->adjustment2->expects($this->once())->method('setAdjustable')->with($this->orderItemUnit);
        $this->adjustment2->expects($this->atLeastOnce())->method('getAmount')->willReturn(50);

        $this->orderItemUnit->addAdjustment($this->adjustment1);
        $this->orderItemUnit->addAdjustment($this->adjustment2);

        $this->orderItem->expects($this->once())->method('getUnitPrice')->willReturn(500);

        $this->assertSame(650, $this->orderItemUnit->getTotal());
    }

    public function testRecalculatesTotalProperlyAfterAdjustmentAmountChange(): void
    {
        $this->orderItem->expects($this->once())->method('getUnitPrice')->willReturn(1000);
        $this->orderItem->expects($this->exactly(2))->method('recalculateUnitsTotal');

        $this->adjustment1->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);
        $this->adjustment1->expects($this->atLeastOnce())->method('getAmount')->willReturn(100);
        $this->adjustment1->expects($this->once())->method('setAdjustable')->with($this->orderItemUnit);

        $this->adjustment2->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);
        $this->adjustment2->expects($this->atLeastOnce())->method('getAmount')->willReturn(150);
        $this->adjustment2->expects($this->once())->method('setAdjustable')->with($this->orderItemUnit);

        $this->orderItemUnit->addAdjustment($this->adjustment1);
        $this->orderItemUnit->addAdjustment($this->adjustment2);

        $this->orderItemUnit->recalculateAdjustmentsTotal();

        $this->assertSame(1000 + 100 + 150, $this->orderItemUnit->getTotal());
    }
}
