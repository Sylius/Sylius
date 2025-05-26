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

namespace Tests\Sylius\Component\Order\Factory;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Order\Factory\OrderItemUnitFactory;
use Sylius\Component\Order\Factory\OrderItemUnitFactoryInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Model\OrderItemUnit;
use Sylius\Component\Order\Model\OrderItemUnitInterface;
use Sylius\Resource\Exception\UnsupportedMethodException;

final class OrderItemUnitFactoryTest extends TestCase
{
    /** @var OrderItemUnitFactoryInterface<OrderItemUnitInterface> */
    private OrderItemUnitFactoryInterface $factory;

    protected function setUp(): void
    {
        $this->factory = new OrderItemUnitFactory(OrderItemUnit::class);
    }

    public function testItImplementsFactoryInterface(): void
    {
        $this->assertInstanceOf(OrderItemUnitFactoryInterface::class, $this->factory);
    }

    public function testItThrowsExceptionWhenCreatingUnitWithoutOrderItem(): void
    {
        $this->expectException(UnsupportedMethodException::class);
        $this->factory->createNew();
    }

    public function testItCreatesNewOrderItemUnitForGivenOrderItem(): void
    {
        $orderItem = $this->createMock(OrderItemInterface::class);

        /** @var OrderItemUnitInterface&MockObject $unit */
        $unit = $this->factory->createForItem($orderItem);

        $this->assertInstanceOf(OrderItemUnitInterface::class, $unit);
        $this->assertSame($orderItem, $unit->getOrderItem());
    }
}
