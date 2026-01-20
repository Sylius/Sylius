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

namespace Tests\Sylius\Component\Core\Promotion\Action;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Promotion\Action\ShippingPercentageDiscountPromotionActionCommand;
use Sylius\Component\Promotion\Action\PromotionActionCommandInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Resource\Factory\FactoryInterface;

final class ShippingPercentageDiscountPromotionActionCommandTest extends TestCase
{
    private FactoryInterface&MockObject $adjustmentFactory;

    private MockObject&OrderInterface $order;

    private MockObject&PromotionInterface $promotion;

    private MockObject&ShipmentInterface $firstShipment;

    private MockObject&ShipmentInterface $secondShipment;

    private AdjustmentInterface&MockObject $firstAdjustment;

    private AdjustmentInterface&MockObject $secondAdjustment;

    private ShippingPercentageDiscountPromotionActionCommand $command;

    protected function setUp(): void
    {
        $this->order = $this->createMock(OrderInterface::class);
        $this->promotion = $this->createMock(PromotionInterface::class);
        $this->firstShipment = $this->createMock(ShipmentInterface::class);
        $this->secondShipment = $this->createMock(ShipmentInterface::class);
        $this->firstAdjustment = $this->createMock(AdjustmentInterface::class);
        $this->secondAdjustment = $this->createMock(AdjustmentInterface::class);
        $this->adjustmentFactory = $this->createMock(FactoryInterface::class);
        $this->command = new ShippingPercentageDiscountPromotionActionCommand($this->adjustmentFactory);
    }

    public function testShouldImplementPromotionActionInterface(): void
    {
        $this->assertInstanceOf(PromotionActionCommandInterface::class, $this->command);
    }

    public function testShouldAppliesPercentageDiscountOnEveryShipment(): void
    {
        $this->promotion->expects($this->exactly(2))->method('getName')->willReturn('Promotion');
        $this->promotion->expects($this->exactly(2))->method('getCode')->willReturn('PROMOTION');
        $this->order->expects($this->once())->method('hasShipments')->willReturn(true);
        $this->order->expects($this->once())->method('getShipments')->willReturn(new ArrayCollection([
            $this->firstShipment,
            $this->secondShipment,
        ]));
        $this->adjustmentFactory->expects($this->exactly(2))->method('createNew')->willReturnOnConsecutiveCalls(
            $this->firstAdjustment,
            $this->secondAdjustment,
        );
        $this->firstShipment->expects($this->exactly(3))->method('getAdjustmentsTotal')->willReturnMap([
            [AdjustmentInterface::SHIPPING_ADJUSTMENT, 400],
            [AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT, 0],
            [AdjustmentInterface::SHIPPING_ADJUSTMENT, 400],
        ]);
        $this->firstAdjustment->expects($this->once())->method('setType')->with(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT);
        $this->firstAdjustment->expects($this->once())->method('setLabel')->with('Promotion');
        $this->firstAdjustment->expects($this->once())->method('setOriginCode')->with('PROMOTION');
        $this->firstAdjustment->expects($this->once())->method('setAmount')->with(-200);
        $this->firstShipment->expects($this->once())->method('addAdjustment')->with($this->firstAdjustment);

        $this->secondShipment->expects($this->exactly(3))->method('getAdjustmentsTotal')->willReturnMap([
            [AdjustmentInterface::SHIPPING_ADJUSTMENT, 600],
            [AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT, 0],
            [AdjustmentInterface::SHIPPING_ADJUSTMENT, 600],
        ]);
        $this->secondAdjustment->expects($this->once())->method('setType')->with(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT);
        $this->secondAdjustment->expects($this->once())->method('setLabel')->with('Promotion');
        $this->secondAdjustment->expects($this->once())->method('setOriginCode')->with('PROMOTION');
        $this->secondAdjustment->expects($this->once())->method('setAmount')->with(-300);
        $this->secondShipment->expects($this->once())->method('addAdjustment')->with($this->secondAdjustment);

        $this->assertTrue(
            $this->command->execute($this->order, ['percentage' => 0.5], $this->promotion),
        );
    }

    public function testShouldNotApplyDiscountIfOrderHasNoShipment(): void
    {
        $this->order->expects($this->once())->method('hasShipments')->willReturn(false);
        $this->order->expects($this->never())->method('getShipments');
        $this->adjustmentFactory->expects($this->never())->method('createNew');

        $this->assertFalse(
            $this->command->execute($this->order, ['percentage' => 0.5], $this->promotion),
        );
    }

    public function testShouldNotApplyDiscountIfAdjustmentAmountWouldBeZero(): void
    {
        $this->order->expects($this->once())->method('hasShipments')->willReturn(true);
        $this->order->expects($this->once())->method('getShipments')->willReturn(new ArrayCollection([$this->firstShipment]));
        $this->firstShipment->expects($this->exactly(3))->method('getAdjustmentsTotal')->willReturnMap([
            [AdjustmentInterface::SHIPPING_ADJUSTMENT, 0],
            [AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT, 0],
            [AdjustmentInterface::SHIPPING_ADJUSTMENT, 0],
        ]);
        $this->adjustmentFactory->expects($this->never())->method('createNew');

        $this->assertFalse(
            $this->command->execute($this->order, ['percentage' => 0.5], $this->promotion),
        );
    }

    public function testShouldThrowExceptionIfSubjectIsNotOrder(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->command->execute(
            $this->createMock(PromotionSubjectInterface::class),
            ['percentage' => 0.5],
            $this->promotion,
        );
    }

    public function testShouldRevertAdjustments(): void
    {
        $this->promotion->expects($this->exactly(4))->method('getCode')->willReturn('PROMOTION');
        $this->firstAdjustment->expects($this->exactly(2))->method('getOriginCode')->willReturn('PROMOTION');
        $this->secondAdjustment->expects($this->exactly(2))->method('getOriginCode')->willReturn('OTHER_PROMOTION');
        $this->order->expects($this->once())->method('hasShipments')->willReturn(true);
        $this->order->expects($this->once())->method('getShipments')->willReturn(new ArrayCollection([
            $this->firstShipment,
            $this->secondShipment,
        ]));
        $this->order
            ->expects($this->once())
            ->method('getAdjustments')
            ->with(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$this->firstAdjustment, $this->secondAdjustment]));

        $this->order->expects($this->once())->method('removeAdjustment')->with($this->firstAdjustment);
        $this->firstShipment
            ->expects($this->once())
            ->method('getAdjustments')
            ->with(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$this->firstAdjustment]));
        $this->firstShipment->expects($this->once())->method('removeAdjustment')->with($this->firstAdjustment);
        $this->secondShipment
            ->expects($this->once())
            ->method('getAdjustments')
            ->with(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$this->secondAdjustment]));
        $this->secondShipment->expects($this->never())->method('removeAdjustment')->with($this->firstAdjustment);

        $this->command->revert($this->order, [], $this->promotion);
    }

    public function testShouldNotRevertAdjustmentsIfOrderHasNoShipment(): void
    {
        $this->order->expects($this->once())->method('hasShipments')->willReturn(false);
        $this->order->expects($this->never())->method('getShipments');

        $this->command->revert($this->order, [], $this->promotion);
    }

    public function testShouldThrowExceptionWhileRevertingSubjectIfItIsNotOrder(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->command->revert(
            $this->createMock(PromotionSubjectInterface::class),
            [],
            $this->promotion,
        );
    }
}
