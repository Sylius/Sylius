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

namespace Tests\Sylius\Component\Order\Modifier;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifier;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

final class OrderModifierTest extends TestCase
{
    private MockObject&OrderProcessorInterface $orderProcessor;

    private MockObject&OrderItemQuantityModifierInterface $orderItemQuantityModifier;

    private OrderModifier $orderModifier;

    private MockObject&OrderInterface $order;

    private MockObject&OrderItemInterface $orderItem;

    private MockObject&OrderItemInterface $existingItem;

    private MockObject&OrderItemInterface $newItem;

    protected function setUp(): void
    {
        $this->orderProcessor = $this->createMock(OrderProcessorInterface::class);
        $this->orderItemQuantityModifier = $this->createMock(OrderItemQuantityModifierInterface::class);
        $this->orderModifier = new OrderModifier($this->orderProcessor, $this->orderItemQuantityModifier);
        $this->order = $this->createMock(OrderInterface::class);
        $this->orderItem = $this->createMock(OrderItemInterface::class);
        $this->existingItem = $this->createMock(OrderItemInterface::class);
        $this->newItem = $this->createMock(OrderItemInterface::class);
    }

    public function testImplementsAnOrderModifierInterface(): void
    {
        $this->assertInstanceOf(OrderModifierInterface::class, $this->orderModifier);
    }

    public function testAddsNewItemToOrderIfItIsEmpty(): void
    {
        $this->order->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([]));
        $this->order->expects($this->once())->method('addItem')->with($this->orderItem);

        $this->orderProcessor->expects($this->once())->method('process')->with($this->order);

        $this->orderModifier->addToOrder($this->order, $this->orderItem);
    }

    public function testAddsNewItemToAnOrderIfDifferentOrderItemIsInAnOrder(): void
    {
        $this->order
            ->expects($this->once())
            ->method('getItems')
            ->willReturn(new ArrayCollection([$this->existingItem]))
        ;

        $this->newItem->expects($this->once())->method('equals')->with($this->existingItem)->willReturn(false);

        $this->orderItemQuantityModifier->expects($this->never())->method('modify');

        $this->order->expects($this->once())->method('addItem')->with($this->newItem);

        $this->orderProcessor->expects($this->once())->method('process')->with($this->order);

        $this->orderModifier->addToOrder($this->order, $this->newItem);
    }

    public function testChangesQuantityOfAnItemIfSameOrderItemAlreadyExists(): void
    {
        $this->order
            ->expects($this->once())
            ->method('getItems')
            ->willReturn(new ArrayCollection([$this->existingItem]))
        ;

        $this->newItem->expects($this->once())->method('equals')->with($this->existingItem)->willReturn(true);

        $this->existingItem->expects($this->once())->method('getQuantity')->willReturn(2);

        $this->newItem->expects($this->once())->method('getQuantity')->willReturn(3);

        $this->order->expects($this->never())->method('addItem')->with($this->existingItem);

        $this->orderItemQuantityModifier->expects($this->once())->method('modify')->with($this->existingItem, 5);

        $this->orderProcessor->expects($this->once())->method('process')->with($this->order);

        $this->orderModifier->addToOrder($this->order, $this->newItem);
    }

    public function testRemovesAnOrderItemFromAnOrder(): void
    {
        $this->order->expects($this->once())->method('removeItem')->with($this->orderItem);

        $this->orderProcessor->expects($this->once())->method('process')->with($this->order);

        $this->orderModifier->removeFromOrder($this->order, $this->orderItem);
    }
}
