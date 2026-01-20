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
use Sylius\Component\Order\Factory\OrderItemUnitFactoryInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Model\OrderItemUnitInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifier;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;

final class OrderItemQuantityModifierTest extends TestCase
{
    private OrderItemQuantityModifier $orderItemQuantityModifier;

    /** @var OrderItemUnitFactoryInterface<OrderItemUnitInterface>&MockObject */
    private MockObject&OrderItemUnitFactoryInterface $orderItemUnitFactory;

    private MockObject&OrderItemInterface $orderItem;

    protected function setUp(): void
    {
        $this->orderItemUnitFactory = $this->createMock(OrderItemUnitFactoryInterface::class);
        $this->orderItemQuantityModifier = new OrderItemQuantityModifier($this->orderItemUnitFactory);
        $this->orderItem = $this->createMock(OrderItemInterface::class);
    }

    public function testImplementsAnOrderItemQuantityModifierInterface(): void
    {
        $this->assertInstanceOf(OrderItemQuantityModifierInterface::class, $this->orderItemQuantityModifier);
    }

    public function testAddsProperNumberOfOrderItemUnitsToAnOrderItem(): void
    {
        $this->orderItem->expects($this->once())->method('getQuantity')->willReturn(1);

        $this->orderItemQuantityModifier->modify($this->orderItem, 3);
    }

    public function testRemovesUnitsIfTargetQuantityIsLowerThanCurrent(): void
    {
        $unit1 = $this->createMock(OrderItemUnitInterface::class);
        $unit2 = $this->createMock(OrderItemUnitInterface::class);
        $unit3 = $this->createMock(OrderItemUnitInterface::class);
        $unit4 = $this->createMock(OrderItemUnitInterface::class);

        $this->orderItem->expects($this->once())->method('getQuantity')->willReturn(4);
        $this->orderItem
            ->expects($this->once())
            ->method('getUnits')
            ->willReturn(new ArrayCollection([$unit1, $unit2, $unit3, $unit4]))
        ;
        $this->orderItem->expects($this->once())->method('removeUnit')->with($unit1);
        $this->orderItemQuantityModifier->modify($this->orderItem, 3);
    }

    public function testDoesNothingIfTargetQuantityIsEqualToCurrent(): void
    {
        $this->orderItem->expects($this->once())->method('getQuantity')->willReturn(3);

        $this->orderItemUnitFactory->expects($this->never())->method('createForItem');

        $this->orderItem->expects($this->never())->method('addUnit');
        $this->orderItem->expects($this->never())->method('removeUnit');

        $this->orderItemQuantityModifier->modify($this->orderItem, 3);
    }

    public function testDoesNothingIfTargetQuantityIs0(): void
    {
        $this->orderItem->expects($this->once())->method('getQuantity')->willReturn(3);

        $this->orderItemUnitFactory->expects($this->never())->method('createForItem');

        $this->orderItem->expects($this->never())->method('addUnit');
        $this->orderItem->expects($this->never())->method('removeUnit');

        $this->orderItemQuantityModifier->modify($this->orderItem, 0);
    }

    public function testDoesNothingIfTargetQuantityIsBelow0(): void
    {
        $this->orderItem->expects($this->once())->method('getQuantity')->willReturn(3);

        $this->orderItemUnitFactory->expects($this->never())->method('createForItem');

        $this->orderItem->expects($this->never())->method('addUnit');
        $this->orderItem->expects($this->never())->method('removeUnit');

        $this->orderItemQuantityModifier->modify($this->orderItem, -10);
    }
}
