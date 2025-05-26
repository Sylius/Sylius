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

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\OrderProcessing\OrderPricesRecalculator;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

final class OrderPricesRecalculatorTest extends TestCase
{
    private MockObject&OrderInterface $order;

    private MockObject&ProductVariantPricesCalculatorInterface $productVariantPriceCalculator;

    private OrderPricesRecalculator $orderPricesRecalculator;

    protected function setUp(): void
    {
        $this->order = $this->createMock(OrderInterface::class);
        $this->productVariantPriceCalculator = $this->createMock(ProductVariantPricesCalculatorInterface::class);
        $this->orderPricesRecalculator = new OrderPricesRecalculator($this->productVariantPriceCalculator);
    }

    public function testShouldImplementOrderProcessorInterface(): void
    {
        $this->assertInstanceOf(OrderProcessorInterface::class, $this->orderPricesRecalculator);
    }

    public function testShouldRecalculatesPricesAddingCustomerToTheContext(): void
    {
        $item = $this->createMock(OrderItemInterface::class);
        $variant = $this->createMock(ProductVariantInterface::class);
        $channel = $this->createMock(ChannelInterface::class);
        $this->order->expects($this->once())->method('canBeProcessed')->willReturn(true);
        $this->order->expects($this->once())->method('getChannel')->willReturn($channel);
        $this->order->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([$item]));
        $item->expects($this->once())->method('isImmutable')->willReturn(false);
        $item->expects($this->exactly(2))->method('getVariant')->willReturn($variant);
        $this->productVariantPriceCalculator
            ->expects($this->once())
            ->method('calculate')
            ->with($variant, ['channel' => $channel])
            ->willReturn(10);
        $this->productVariantPriceCalculator
            ->expects($this->once())
            ->method('calculateOriginal')
            ->with($variant, ['channel' => $channel])
            ->willReturn(20);
        $item->expects($this->once())->method('setUnitPrice')->with(10);
        $item->expects($this->once())->method('setOriginalUnitPrice')->with(20);

        $this->orderPricesRecalculator->process($this->order);
    }

    public function testShouldThrowExceptionIfPassedOrderIsNotCoreOrder(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->orderPricesRecalculator->process($this->createMock(BaseOrderInterface::class));
    }

    public function testShouldDoNothingIfTheOrderCannotBeProcessed(): void
    {
        $this->order->expects($this->once())->method('canBeProcessed')->willReturn(false);
        $this->order->expects($this->never())->method('getChannel');
        $this->order->expects($this->never())->method('getItems');

        $this->orderPricesRecalculator->process($this->order);
    }
}
