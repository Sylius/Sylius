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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderProcessing\OrderPromotionProcessor;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Promotion\Processor\PromotionProcessorInterface;

final class OrderPromotionProcessorTest extends TestCase
{
    private MockObject&PromotionProcessorInterface $promotionProcessor;

    private MockObject&OrderInterface $order;

    private OrderPromotionProcessor $orderPromotionProcessor;

    protected function setUp(): void
    {
        $this->promotionProcessor = $this->createMock(PromotionProcessorInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
        $this->orderPromotionProcessor = new OrderPromotionProcessor($this->promotionProcessor);
    }

    public function testShouldImplementOrderProcessorInterface(): void
    {
        $this->assertInstanceOf(OrderProcessorInterface::class, $this->orderPromotionProcessor);
    }

    public function testShouldProcessPromotions(): void
    {
        $this->order->expects($this->once())->method('canBeProcessed')->willReturn(true);
        $this->promotionProcessor->expects($this->once())->method('process')->with($this->order);

        $this->orderPromotionProcessor->process($this->order);
    }

    public function testShouldDoNothingIfOrderCannotBeProcessed(): void
    {
        $this->order->expects($this->once())->method('canBeProcessed')->willReturn(false);
        $this->promotionProcessor->expects($this->never())->method('process')->with($this->order);

        $this->orderPromotionProcessor->process($this->order);
    }
}
