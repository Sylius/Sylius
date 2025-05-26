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

namespace Tests\Sylius\Component\Core\OrderProcessing;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderProcessing\OrderAdjustmentsClearer;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

final class OrderAdjustmentsClearerTest extends TestCase
{
    private MockObject&OrderInterface $order;

    private OrderAdjustmentsClearer $orderAdjustmentsClearer;

    protected function setUp(): void
    {
        $this->order = $this->createMock(OrderInterface::class);
        $this->orderAdjustmentsClearer = new OrderAdjustmentsClearer([
            AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT,
            AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT,
        ]);
    }

    public function testShouldImplementOrderProcessorInterface(): void
    {
        $this->assertInstanceOf(OrderProcessorInterface::class, $this->orderAdjustmentsClearer);
    }

    public function testShouldRemoveAdjustmentsWithSpecifiedTypesFromOrderRecursively(): void
    {
        $this->order->expects($this->once())->method('canBeProcessed')->willReturn(true);
        $removeInvokedCount = $this->exactly(2);
        $this->order->expects($removeInvokedCount)->method('removeAdjustmentsRecursively')->willReturnCallback(
            function (string $type) use ($removeInvokedCount): void {
                if ($removeInvokedCount->numberOfInvocations() === 1) {
                    $this->assertSame(AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT, $type);
                }
                if ($removeInvokedCount->numberOfInvocations() === 2) {
                    $this->assertSame(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT, $type);
                }
            },
        );

        $this->orderAdjustmentsClearer->process($this->order);
    }

    public function testShouldDoNothingIfTheOrderCannotBeProcessed(): void
    {
        $this->order->expects($this->once())->method('canBeProcessed')->willReturn(false);

        $this->order->expects($this->never())->method('removeAdjustmentsRecursively')->with($this->anything());

        $this->orderAdjustmentsClearer->process($this->order);
    }
}
