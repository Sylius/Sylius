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

namespace Tests\Sylius\Component\Core\Inventory\Checker;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Inventory\Checker\OrderItemAvailabilityChecker;
use Sylius\Component\Core\Inventory\Checker\OrderItemAvailabilityCheckerInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class OrderItemAvailabilityCheckerTest extends TestCase
{
    private MockObject&OrderItemInterface $orderItem;

    private MockObject&ProductVariantInterface $productVariant;

    private OrderItemAvailabilityChecker $checker;

    protected function setUp(): void
    {
        $this->orderItem = $this->createMock(OrderItemInterface::class);
        $this->productVariant = $this->createMock(ProductVariantInterface::class);
        $this->checker = new OrderItemAvailabilityChecker();
    }

    public function testShouldImplementOrderItemAvailabilityCheckerInterface(): void
    {
        $this->assertInstanceOf(OrderItemAvailabilityCheckerInterface::class, $this->checker);
    }

    public function testShouldReturnTrueIfVariantIsUntracked(): void
    {
        $this->orderItem->expects($this->once())->method('getVariant')->willReturn($this->productVariant);
        $this->productVariant->expects($this->once())->method('isTracked')->willReturn(false);

        $this->assertTrue($this->checker->isReservedStockSufficient($this->orderItem));
    }

    public function testShouldReturnTrueIfStockIsSufficient(): void
    {
        $this->orderItem->expects($this->once())->method('getVariant')->willReturn($this->productVariant);
        $this->orderItem->expects($this->once())->method('getQuantity')->willReturn(2);
        $this->productVariant->expects($this->once())->method('isTracked')->willReturn(true);
        $this->productVariant->expects($this->once())->method('getOnHold')->willReturn(2);
        $this->productVariant->expects($this->once())->method('getOnHand')->willReturn(2);

        $this->assertTrue($this->checker->isReservedStockSufficient($this->orderItem));
    }

    public function testShouldReturnFalseIfOnHoldValueIsNotSufficient(): void
    {
        $this->orderItem->expects($this->once())->method('getVariant')->willReturn($this->productVariant);
        $this->orderItem->expects($this->once())->method('getQuantity')->willReturn(2);
        $this->productVariant->expects($this->once())->method('isTracked')->willReturn(true);
        $this->productVariant->expects($this->once())->method('getOnHold')->willReturn(1);

        $this->assertFalse($this->checker->isReservedStockSufficient($this->orderItem));
    }

    public function testShouldReturnFalseIfOnHandValueIsNotSufficient(): void
    {
        $this->orderItem->expects($this->once())->method('getVariant')->willReturn($this->productVariant);
        $this->orderItem->expects($this->once())->method('getQuantity')->willReturn(2);
        $this->productVariant->expects($this->once())->method('isTracked')->willReturn(true);
        $this->productVariant->expects($this->once())->method('getOnHold')->willReturn(2);
        $this->productVariant->expects($this->once())->method('getOnHand')->willReturn(1);

        $this->assertFalse($this->checker->isReservedStockSufficient($this->orderItem));
    }
}
