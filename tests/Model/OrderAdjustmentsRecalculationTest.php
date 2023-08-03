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

namespace Sylius\Tests\Model;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Order\Model\Order;
use Sylius\Component\Order\Model\OrderItem;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Model\OrderItemUnit;

final class OrderAdjustmentsRecalculationTest extends TestCase
{
    private Order $order;
    private OrderItemInterface $item;

    protected function setUp(): void
    {
        $neutralAdjustment = $this->createAdjustment(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT, -150, true);

        $this->order = new Order();
        $this->item = new OrderItem();
        $this->item->setUnitPrice(1000);
        $this->unitNumberOne = new OrderItemUnit($this->item);
        $this->unitNumberTwo = new OrderItemUnit($this->item);
        $this->order->addItem($this->item);
        $this->order->addAdjustment($neutralAdjustment);
    }

    /** @test */
    public function it_recalculates_order_total_properly_with_order_item_adjustments(): void
    {
        $adjustmentNumberOne = $this->createAdjustment(AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT, -300, false);
        $this->item->addAdjustment($adjustmentNumberOne);

        $this->assertEquals(1700, $this->order->getTotal());
        $this->assertEquals(-300, $this->item->getAdjustmentsTotal());

        $adjustmentNumberTwo = $this->createAdjustment(AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT, -155, false);
        $this->item->addAdjustment($adjustmentNumberTwo);

        $this->assertEquals(1545, $this->order->getTotal());
        $this->assertEquals(-455, $this->item->getAdjustmentsTotal());

        $this->item->removeAdjustment($adjustmentNumberOne);

        $this->assertEquals(1845, $this->order->getTotal());
        $this->assertEquals(-155, $this->item->getAdjustmentsTotal());
    }

    /** @test */
    public function it_recalculates_order_total_properly_with_order_adjustments(): void
    {
        $adjustmentNumberOne = $this->createAdjustment(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT, -100, false);
        $this->order->addAdjustment($adjustmentNumberOne);

        $this->assertEquals(1900, $this->order->getTotal());
        $this->assertEquals(-100, $this->order->getAdjustmentsTotal());

        $adjustmentNumberTwo = $this->createAdjustment(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT, -155, false);
        $this->order->addAdjustment($adjustmentNumberTwo);

        $this->assertEquals(1745, $this->order->getTotal());
        $this->assertEquals(-255, $this->order->getAdjustmentsTotal());

        $this->order->removeAdjustment($adjustmentNumberOne);

        $this->assertEquals(1845, $this->order->getTotal());
        $this->assertEquals(-155, $this->order->getAdjustmentsTotal());
    }

    /** @test */
    public function it_recalculates_order_total_properly_with_order_unit_adjustments(): void
    {
        $adjustmentNumberOne = $this->createAdjustment(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT, -200, false);
        $this->unitNumberOne->addAdjustment($adjustmentNumberOne);
        $this->unitNumberTwo->addAdjustment($adjustmentNumberOne);

        $this->assertEquals(1600, $this->order->getTotal());
        $this->assertEquals(-200, $this->unitNumberOne->getAdjustmentsTotal());
        $this->assertEquals(-200, $this->unitNumberTwo->getAdjustmentsTotal());

        $adjustmentNumberTwo = $this->createAdjustment(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT, -125, false);
        $this->unitNumberOne->addAdjustment($adjustmentNumberTwo);
        $this->unitNumberTwo->addAdjustment($adjustmentNumberTwo);

        $this->assertEquals(1350, $this->order->getTotal());
        $this->assertEquals(-325, $this->unitNumberOne->getAdjustmentsTotal());
        $this->assertEquals(-325, $this->unitNumberTwo->getAdjustmentsTotal());

        $this->unitNumberOne->removeAdjustment($adjustmentNumberOne);

        $this->assertEquals(1550, $this->order->getTotal());
        $this->assertEquals(-125, $this->unitNumberOne->getAdjustmentsTotal());
        $this->assertEquals(-325, $this->unitNumberTwo->getAdjustmentsTotal());
    }

    private function createAdjustment(string $type, int $amount, bool $isNeutral): AdjustmentInterface
    {
        $adjustment = $this->createMock(AdjustmentInterface::class);
        $adjustment->method('isNeutral')->willReturn($isNeutral);
        $adjustment->method('getAmount')->willReturn($amount);
        $adjustment->method('getType')->willReturn($type);

        return $adjustment;
    }
}
