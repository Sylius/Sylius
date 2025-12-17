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

namespace Tests\Sylius\Bundle\ApiBundle\Provider;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Provider\AdjustmentOrderProvider;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;

final class AdjustmentOrderProviderTest extends TestCase
{
    private AdjustmentOrderProvider $adjustmentOrderProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adjustmentOrderProvider = new AdjustmentOrderProvider();
    }

    public function testReturnsOrderIfAdjustmentIsForAnOrder(): void
    {
        /** @var AdjustmentInterface|MockObject $adjustmentMock */
        $adjustmentMock = $this->createMock(AdjustmentInterface::class);
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);

        $adjustmentMock->expects(self::once())->method('getAdjustable')->willReturn($orderMock);

        $adjustmentMock->expects(self::once())->method('getOrder')->willReturn($orderMock);

        self::assertSame($orderMock, $this->adjustmentOrderProvider->provide($adjustmentMock));
    }

    public function testReturnsOrderIfAdjustmentIsForAnOrderItem(): void
    {
        /** @var AdjustmentInterface|MockObject $adjustmentMock */
        $adjustmentMock = $this->createMock(AdjustmentInterface::class);
        /** @var OrderItemInterface|MockObject $orderItemMock */
        $orderItemMock = $this->createMock(OrderItemInterface::class);
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);

        $adjustmentMock->expects(self::atLeastOnce())->method('getAdjustable')->willReturn($orderItemMock);

        $adjustmentMock->expects(self::once())->method('getOrderItem')->willReturn($orderItemMock);

        $orderItemMock->expects(self::once())->method('getOrder')->willReturn($orderMock);

        self::assertSame($orderMock, $this->adjustmentOrderProvider->provide($adjustmentMock));
    }

    public function testReturnsOrderIfAdjustmentIsForAnOrderItemUnit(): void
    {
        /** @var AdjustmentInterface|MockObject $adjustmentMock */
        $adjustmentMock = $this->createMock(AdjustmentInterface::class);
        /** @var OrderItemUnitInterface|MockObject $orderItemUnitMock */
        $orderItemUnitMock = $this->createMock(OrderItemUnitInterface::class);
        /** @var OrderItemInterface|MockObject $orderItemMock */
        $orderItemMock = $this->createMock(OrderItemInterface::class);
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);

        $adjustmentMock->expects(self::atLeastOnce())->method('getAdjustable')->willReturn($orderItemUnitMock);

        $adjustmentMock->expects(self::once())->method('getOrderItemUnit')->willReturn($orderItemUnitMock);

        $orderItemUnitMock->expects(self::once())->method('getOrderItem')->willReturn($orderItemMock);

        $orderItemMock->expects(self::once())->method('getOrder')->willReturn($orderMock);

        self::assertSame($orderMock, $this->adjustmentOrderProvider->provide($adjustmentMock));
    }

    public function testReturnsNullIfAdjustmentIsNotForKnownType(): void
    {
        /** @var AdjustmentInterface|MockObject $adjustmentMock */
        $adjustmentMock = $this->createMock(AdjustmentInterface::class);

        $adjustmentMock->expects(self::atLeastOnce())->method('getAdjustable')->willReturn(null);

        self::assertNull($this->adjustmentOrderProvider->provide($adjustmentMock));
    }
}
