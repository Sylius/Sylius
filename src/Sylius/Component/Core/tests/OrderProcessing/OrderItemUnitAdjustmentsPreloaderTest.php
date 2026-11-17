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
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\OrderProcessing\OrderItemUnitAdjustmentsPreloader;
use Sylius\Component\Core\Repository\OrderItemUnitRepositoryInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

#[AllowMockObjectsWithoutExpectations]
final class OrderItemUnitAdjustmentsPreloaderTest extends TestCase
{
    private MockObject&OrderItemUnitRepositoryInterface $orderItemUnitRepository;

    private MockObject&OrderInterface $order;

    private OrderItemUnitAdjustmentsPreloader $orderItemUnitAdjustmentsPreloader;

    protected function setUp(): void
    {
        $this->orderItemUnitRepository = $this->createMock(OrderItemUnitRepositoryInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
        $this->orderItemUnitAdjustmentsPreloader = new OrderItemUnitAdjustmentsPreloader($this->orderItemUnitRepository);
    }

    public function testShouldImplementOrderProcessorInterface(): void
    {
        $this->assertInstanceOf(OrderProcessorInterface::class, $this->orderItemUnitAdjustmentsPreloader);
    }

    public function testShouldPreloadAdjustmentsOfEveryUnitOfEveryOrderItemInOneCall(): void
    {
        $firstUnit = $this->createMock(OrderItemUnitInterface::class);
        $firstUnit->method('getId')->willReturn(1);

        $secondUnit = $this->createMock(OrderItemUnitInterface::class);
        $secondUnit->method('getId')->willReturn(2);

        $thirdUnit = $this->createMock(OrderItemUnitInterface::class);
        $thirdUnit->method('getId')->willReturn(3);

        $firstItem = $this->createMock(OrderItemInterface::class);
        $firstItem->method('getUnits')->willReturn(new ArrayCollection([$firstUnit, $secondUnit]));

        $secondItem = $this->createMock(OrderItemInterface::class);
        $secondItem->method('getUnits')->willReturn(new ArrayCollection([$thirdUnit]));

        $this->order->expects($this->once())->method('canBeProcessed')->willReturn(true);
        $this->order->method('getItems')->willReturn(new ArrayCollection([$firstItem, $secondItem]));

        $this->orderItemUnitRepository->expects($this->once())
            ->method('preloadAdjustments')
            ->with([$firstUnit, $secondUnit, $thirdUnit])
        ;

        $this->orderItemUnitAdjustmentsPreloader->process($this->order);
    }

    public function testShouldSkipUnitsWithoutAnIdYet(): void
    {
        $persistedUnit = $this->createMock(OrderItemUnitInterface::class);
        $persistedUnit->method('getId')->willReturn(1);

        $newUnit = $this->createMock(OrderItemUnitInterface::class);
        $newUnit->method('getId')->willReturn(null);

        $item = $this->createMock(OrderItemInterface::class);
        $item->method('getUnits')->willReturn(new ArrayCollection([$persistedUnit, $newUnit]));

        $this->order->expects($this->once())->method('canBeProcessed')->willReturn(true);
        $this->order->method('getItems')->willReturn(new ArrayCollection([$item]));

        $this->orderItemUnitRepository->expects($this->once())
            ->method('preloadAdjustments')
            ->with([$persistedUnit])
        ;

        $this->orderItemUnitAdjustmentsPreloader->process($this->order);
    }

    public function testShouldDoNothingIfTheOrderCannotBeProcessed(): void
    {
        $this->order->expects($this->once())->method('canBeProcessed')->willReturn(false);

        $this->order->expects($this->never())->method('getItems');
        $this->orderItemUnitRepository->expects($this->never())->method('preloadAdjustments');

        $this->orderItemUnitAdjustmentsPreloader->process($this->order);
    }
}
