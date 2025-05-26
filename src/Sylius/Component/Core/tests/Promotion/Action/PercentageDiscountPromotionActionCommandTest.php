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
use Sylius\Component\Core\Distributor\MinimumPriceDistributorInterface;
use Sylius\Component\Core\Distributor\ProportionalIntegerDistributorInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Promotion\Action\PercentageDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Applicator\UnitsPromotionAdjustmentsApplicatorInterface;
use Sylius\Component\Promotion\Action\PromotionActionCommandInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

final class PercentageDiscountPromotionActionCommandTest extends TestCase
{
    private MockObject&ProportionalIntegerDistributorInterface $distributor;

    private MockObject&UnitsPromotionAdjustmentsApplicatorInterface $unitsPromotionAdjustmentsApplicator;

    private MinimumPriceDistributorInterface&MockObject $minimumPriceDistributor;

    private MockObject&OrderInterface $order;

    private MockObject&OrderItemInterface $firstItem;

    private MockObject&OrderItemInterface $secondItem;

    private MockObject&PromotionInterface $promotion;

    private ChannelInterface&MockObject $channel;

    private PercentageDiscountPromotionActionCommand $command;

    protected function setUp(): void
    {
        $this->distributor = $this->createMock(ProportionalIntegerDistributorInterface::class);
        $this->unitsPromotionAdjustmentsApplicator = $this->createMock(UnitsPromotionAdjustmentsApplicatorInterface::class);
        $this->minimumPriceDistributor = $this->createMock(MinimumPriceDistributorInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
        $this->firstItem = $this->createMock(OrderItemInterface::class);
        $this->secondItem = $this->createMock(OrderItemInterface::class);
        $this->promotion = $this->createMock(PromotionInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->command = new PercentageDiscountPromotionActionCommand(
            $this->distributor,
            $this->unitsPromotionAdjustmentsApplicator,
            $this->minimumPriceDistributor,
        );
    }

    public function testShouldImplementPromotionActionInterface(): void
    {
        $this->assertInstanceOf(PromotionActionCommandInterface::class, $this->command);
    }

    public function testShouldUseDistributorAndApplicatorToExecutePromotionAction(): void
    {
        $this->order->expects($this->once())->method('countItems')->willReturn(2);
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->order->expects($this->once())->method('getItems')
            ->willReturn(new ArrayCollection([$this->firstItem, $this->secondItem]));
        $this->promotion->expects($this->exactly(2))->method('getAppliesToDiscounted')->willReturn(true);
        $this->order->expects($this->once())->method('getPromotionSubjectTotal')->willReturn(10000);
        $this->minimumPriceDistributor->expects($this->once())->method('distribute')
            ->with([$this->firstItem, $this->secondItem], -1000, $this->channel, true)
            ->willReturn([-200, -800]);
        $this->unitsPromotionAdjustmentsApplicator->expects($this->once())->method('apply')
            ->with($this->order, $this->promotion, [-200, -800]);

        $this->assertTrue(
            $this->command->execute($this->order, ['percentage' => 0.1], $this->promotion),
        );
    }

    public function testShouldDistributePromotionUsingRegularDistributorIfMinimumPriceIsNotProvided(): void
    {
        $this->command = new PercentageDiscountPromotionActionCommand(
            $this->distributor,
            $this->unitsPromotionAdjustmentsApplicator,
        );
        $this->order->expects($this->once())->method('countItems')->willReturn(2);
        $this->order->expects($this->once())->method('getItems')
            ->willReturn(new ArrayCollection([$this->firstItem, $this->secondItem]));
        $this->firstItem->expects($this->once())->method('getTotal')->willReturn(200);
        $this->secondItem->expects($this->once())->method('getTotal')->willReturn(800);
        $this->promotion->expects($this->exactly(3))->method('getAppliesToDiscounted')->willReturn(true);
        $this->order->expects($this->once())->method('getPromotionSubjectTotal')->willReturn(10000);
        $this->minimumPriceDistributor->expects($this->never())->method('distribute');
        $this->distributor->expects($this->once())->method('distribute')
            ->with([200, 800], -1000)
            ->willReturn([-200, -800]);
        $this->unitsPromotionAdjustmentsApplicator->expects($this->once())->method('apply')
            ->with($this->order, $this->promotion, [-200, -800]);

        $this->assertTrue(
            $this->command->execute($this->order, ['percentage' => 0.1], $this->promotion),
        );
    }

    public function testShouldDistributePromotionAmountTakingMinimumPriceIntoAccount(): void
    {
        $this->order->expects($this->once())->method('countItems')->willReturn(2);
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->order->expects($this->once())->method('getItems')
            ->willReturn(new ArrayCollection([$this->firstItem, $this->secondItem]));
        $this->order->expects($this->once())->method('getPromotionSubjectTotal')->willReturn(1000);
        $this->promotion->expects($this->exactly(2))->method('getAppliesToDiscounted')->willReturn(true);
        $this->minimumPriceDistributor->expects($this->once())
            ->method('distribute')
            ->with([$this->firstItem, $this->secondItem], -500, $this->channel, true)
            ->willReturn([-100, -400]);
        $this->unitsPromotionAdjustmentsApplicator->expects($this->once())
            ->method('apply')
            ->with($this->order, $this->promotion, [-100, -400]);

        $this->assertTrue(
            $this->command->execute($this->order, ['percentage' => 0.5], $this->promotion),
        );
    }

    public function testShouldUseDistributorAndApplicatorToExecutePromotionActionOnlyForNonDiscountedItems(): void
    {
        $this->order->expects($this->once())->method('countItems')->willReturn(2);
        $this->order->expects($this->once())->method('getItems')
            ->willReturn(new ArrayCollection([$this->firstItem, $this->secondItem]));
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->order->expects($this->once())->method('getNonDiscountedItemsTotal')->willReturn(200);
        $this->promotion->expects($this->exactly(2))->method('getAppliesToDiscounted')->willReturn(false);
        $this->distributor->expects($this->never())->method('distribute');
        $this->minimumPriceDistributor->expects($this->once())
            ->method('distribute')
            ->with([$this->firstItem, $this->secondItem], -20, $this->channel, false)
            ->willReturn([-20, 0]);
        $this->unitsPromotionAdjustmentsApplicator->expects($this->once())
            ->method('apply')
            ->with($this->order, $this->promotion, [-20, 0]);

        $this->assertTrue(
            $this->command->execute($this->order, ['percentage' => 0.1], $this->promotion),
        );
    }

    public function testShouldNotApplyDiscountIfOrderHasNoItems(): void
    {
        $this->order->expects($this->once())->method('countItems')->willReturn(0);
        $this->order->expects($this->never())->method('getPromotionSubjectTotal');

        $this->assertFalse($this->command->execute($this->order, ['percentage' => 0.1], $this->promotion));
    }

    public function testShouldNotApplyDiscountIfConfigurationIsInvalid(): void
    {
        $this->order->expects($this->exactly(2))->method('countItems')->willReturn(1);

        $this->assertFalse($this->command->execute($this->order, [], $this->promotion));
        $this->assertFalse($this->command->execute($this->order, ['percentage' => 'string'], $this->promotion));
    }

    public function testShouldThrowExceptionIfSubjectIsNotOrder(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->command->execute($this->createMock(PromotionSubjectInterface::class), [], $this->promotion);
    }

    public function testShouldRevertOrderUnitsOrderPromotionAdjustments(): void
    {
        $unit = $this->createMock(OrderItemUnitInterface::class);
        $firstAdjustment = $this->createMock(AdjustmentInterface::class);
        $secondAdjustment = $this->createMock(AdjustmentInterface::class);
        $this->order->expects($this->once())->method('countItems')->willReturn(1);
        $this->order->expects($this->once())->method('getItems')->willReturn(
            new ArrayCollection([$this->firstItem]),
        );
        $this->firstItem->expects($this->once())->method('getUnits')->willReturn(
            new ArrayCollection([$unit]),
        );
        $unit->expects($this->once())
            ->method('getAdjustments')
            ->with(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$firstAdjustment, $secondAdjustment]));
        $this->promotion->expects($this->exactly(2))->method('getCode')->willReturn('PROMOTION');
        $firstAdjustment->expects($this->once())->method('getOriginCode')->willReturn('PROMOTION');
        $secondAdjustment->expects($this->once())->method('getOriginCode')->willReturn('OTHER_PROMOTION');
        $unit->expects($this->once())->method('removeAdjustment')->with($firstAdjustment);

        $this->command->revert($this->order, [], $this->promotion);
    }

    public function testShouldNotRevertIfOrderHasNoItems(): void
    {
        $this->order->expects($this->once())->method('countItems')->willReturn(0);
        $this->order->expects($this->never())->method('getItems');

        $this->command->revert($this->order, [], $this->promotion);
    }

    public function testShouldThrowExceptionWhileRevertingSubjectWhichIsNotOrder(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->command->revert($this->createMock(PromotionSubjectInterface::class), [], $this->promotion);
    }
}
