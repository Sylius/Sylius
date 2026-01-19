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
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Promotion\Action\UnitDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\UnitPercentageDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Filter\FilterInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Resource\Exception\UnexpectedTypeException;
use Sylius\Resource\Factory\FactoryInterface;

final class UnitPercentageDiscountPromotionActionCommandTest extends TestCase
{
    private FactoryInterface&MockObject $adjustmentFactory;

    private FilterInterface&MockObject $priceRangeFilter;

    private FilterInterface&MockObject $taxonFilter;

    private FilterInterface&MockObject $productFilter;

    private MockObject&PromotionInterface $promotion;

    private ChannelInterface&MockObject $channel;

    private MockObject&OrderInterface $order;

    private MockObject&OrderItemInterface $firstOrderItem;

    private MockObject&OrderItemInterface $secondOrderItem;

    private AdjustmentInterface&MockObject $firstPromotionAdjustment;

    private AdjustmentInterface&MockObject $secondPromotionAdjustment;

    private MockObject&OrderItemUnitInterface $firstUnit;

    private MockObject&OrderItemUnitInterface $secondUnit;

    private MockObject&ProductVariantInterface $firstProductVariant;

    private MockObject&ProductVariantInterface $secondProductVariant;

    private ChannelPricingInterface&MockObject $firstChannelPricing;

    private ChannelPricingInterface&MockObject $secondChannelPricing;

    private Collection&MockObject $items;

    private Collection&MockObject $units;

    private UnitPercentageDiscountPromotionActionCommand $command;

    protected function setUp(): void
    {
        $this->adjustmentFactory = $this->createMock(FactoryInterface::class);
        $this->priceRangeFilter = $this->createMock(FilterInterface::class);
        $this->taxonFilter = $this->createMock(FilterInterface::class);
        $this->productFilter = $this->createMock(FilterInterface::class);
        $this->promotion = $this->createMock(PromotionInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
        $this->firstOrderItem = $this->createMock(OrderItemInterface::class);
        $this->secondOrderItem = $this->createMock(OrderItemInterface::class);
        $this->firstPromotionAdjustment = $this->createMock(AdjustmentInterface::class);
        $this->secondPromotionAdjustment = $this->createMock(AdjustmentInterface::class);
        $this->firstUnit = $this->createMock(OrderItemUnitInterface::class);
        $this->secondUnit = $this->createMock(OrderItemUnitInterface::class);
        $this->firstProductVariant = $this->createMock(ProductVariantInterface::class);
        $this->secondProductVariant = $this->createMock(ProductVariantInterface::class);
        $this->firstChannelPricing = $this->createMock(ChannelPricingInterface::class);
        $this->secondChannelPricing = $this->createMock(ChannelPricingInterface::class);
        $this->items = $this->createMock(Collection::class);
        $this->units = $this->createMock(Collection::class);
        $this->command = new UnitPercentageDiscountPromotionActionCommand(
            $this->adjustmentFactory,
            $this->priceRangeFilter,
            $this->taxonFilter,
            $this->productFilter,
        );
    }

    public function testShouldBeItemDiscountAction(): void
    {
        $this->assertInstanceOf(UnitDiscountPromotionActionCommand::class, $this->command);
    }

    public function testShouldApplyPercentageDiscountOnEveryUnitInOrder(): void
    {
        $this->order->expects($this->exactly(4))->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('getCode')->willReturn('WEB_US');
        $this->firstUnit->expects($this->once())->method('getOrderItem')->willReturn($this->firstOrderItem);
        $this->secondUnit->expects($this->once())->method('getOrderItem')->willReturn($this->secondOrderItem);
        $this->firstUnit->expects($this->once())->method('getTotal')->willReturn(500);
        $this->secondUnit->expects($this->once())->method('getTotal')->willReturn(500);
        $this->firstOrderItem->expects($this->once())->method('getVariant')->willReturn($this->firstProductVariant);
        $this->secondOrderItem->expects($this->once())->method('getVariant')->willReturn($this->secondProductVariant);
        $this->firstOrderItem->expects($this->once())->method('getOrder')->willReturn($this->order);
        $this->secondOrderItem->expects($this->once())->method('getOrder')->willReturn($this->order);
        $this->firstProductVariant
            ->expects($this->once())
            ->method('getChannelPricingForChannel')
            ->with($this->channel)
            ->willReturn($this->firstChannelPricing);
        $this->secondProductVariant
            ->expects($this->once())
            ->method('getChannelPricingForChannel')
            ->with($this->channel)
            ->willReturn($this->secondChannelPricing);
        $this->firstChannelPricing->expects($this->once())->method('getMinimumPrice')->willReturn(0);
        $this->secondChannelPricing->expects($this->once())->method('getMinimumPrice')->willReturn(0);
        $this->order->expects($this->once())->method('getItems')->willReturn($this->items);
        $this->items->expects($this->once())->method('toArray')->willReturn([$this->firstOrderItem]);
        $this->priceRangeFilter
            ->expects($this->once())
            ->method('filter')
            ->with([$this->firstOrderItem], ['percentage' => 0.2, 'channel' => $this->channel])
            ->willReturn([$this->firstOrderItem]);
        $this->taxonFilter
            ->expects($this->once())
            ->method('filter')
            ->with([$this->firstOrderItem], ['percentage' => 0.2])
            ->willReturn([$this->firstOrderItem]);
        $this->productFilter
            ->expects($this->once())
            ->method('filter')
            ->with([$this->firstOrderItem], ['percentage' => 0.2])
            ->willReturn([$this->firstOrderItem]);
        $this->firstOrderItem->expects($this->once())->method('getUnits')->willReturn($this->units);
        $this->units->expects($this->once())->method('getIterator')->willReturn(new \ArrayIterator([
            $this->firstUnit, $this->secondUnit,
        ]));
        $this->firstOrderItem->expects($this->once())->method('getUnitPrice')->willReturn(500);
        $this->promotion->expects($this->exactly(2))->method('getName')->willReturn('Test promotion');
        $this->promotion->expects($this->exactly(2))->method('getCode')->willReturn('TEST_PROMOTION');
        $this->promotion->expects($this->exactly(2))->method('getAppliesToDiscounted')->willReturn(true);
        $this->adjustmentFactory->expects($this->exactly(2))->method('createNew')->willReturnOnConsecutiveCalls(
            $this->firstPromotionAdjustment,
            $this->secondPromotionAdjustment,
        );
        $this->firstPromotionAdjustment
            ->expects($this->once())
            ->method('setType')
            ->with(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT);
        $this->firstPromotionAdjustment->expects($this->once())->method('setLabel')->with('Test promotion');
        $this->firstPromotionAdjustment->expects($this->once())->method('setAmount')->with(-100);
        $this->firstPromotionAdjustment->expects($this->once())->method('setOriginCode')->with('TEST_PROMOTION');
        $this->secondPromotionAdjustment
            ->expects($this->once())
            ->method('setType')
            ->with(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT);
        $this->secondPromotionAdjustment->expects($this->once())->method('setLabel')->with('Test promotion');
        $this->secondPromotionAdjustment->expects($this->once())->method('setAmount')->with(-100);
        $this->secondPromotionAdjustment->expects($this->once())->method('setOriginCode')->with('TEST_PROMOTION');
        $this->firstUnit->expects($this->once())->method('addAdjustment')->with($this->firstPromotionAdjustment);
        $this->secondUnit->expects($this->once())->method('addAdjustment')->with($this->secondPromotionAdjustment);

        $this->assertTrue(
            $this->command->execute($this->order, ['WEB_US' => ['percentage' => 0.2]], $this->promotion),
        );
    }

    public function testShouldApplyDiscountOnlyToNonDiscountedUnitsIfPromotionDoesNotApplyToDiscountedOnes(): void
    {
        $this->order->expects($this->exactly(5))->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('getCode')->willReturn('WEB_US');
        $this->order->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([
            $this->firstOrderItem, $this->secondOrderItem,
        ]));
        $this->priceRangeFilter
            ->expects($this->once())
            ->method('filter')
            ->with([$this->firstOrderItem, $this->secondOrderItem], ['percentage' => 0.2, 'channel' => $this->channel])
            ->willReturn([$this->firstOrderItem, $this->secondOrderItem]);
        $this->taxonFilter
            ->expects($this->once())
            ->method('filter')
            ->with([$this->firstOrderItem, $this->secondOrderItem], ['percentage' => 0.2])
            ->willReturn([$this->firstOrderItem, $this->secondOrderItem]);
        $this->productFilter
            ->expects($this->once())
            ->method('filter')
            ->with([$this->firstOrderItem, $this->secondOrderItem], ['percentage' => 0.2])
            ->willReturn([$this->firstOrderItem, $this->secondOrderItem]);
        $this->firstOrderItem->expects($this->once())->method('getUnitPrice')->willReturn(500);
        $this->firstOrderItem->expects($this->once())->method('getUnits')->willReturn(new ArrayCollection([$this->firstUnit]));
        $this->firstOrderItem->expects($this->exactly(2))->method('getVariant')->willReturn($this->firstProductVariant);
        $this->firstOrderItem->expects($this->exactly(2))->method('getOrder')->willReturn($this->order);
        $this->firstProductVariant
            ->expects($this->once())
            ->method('getAppliedPromotionsForChannel')
            ->with($this->channel)
            ->willReturn(new ArrayCollection([]));
        $this->firstUnit->expects($this->exactly(2))->method('getOrderItem')->willReturn($this->firstOrderItem);
        $this->firstUnit->expects($this->once())->method('getTotal')->willReturn(500);

        $this->secondOrderItem->expects($this->once())->method('getUnitPrice')->willReturn(500);
        $this->secondOrderItem->expects($this->once())->method('getUnits')->willReturn(new ArrayCollection([$this->secondUnit]));
        $this->secondOrderItem->expects($this->once())->method('getVariant')->willReturn($this->secondProductVariant);
        $this->secondOrderItem->expects($this->once())->method('getOrder')->willReturn($this->order);
        $this->secondProductVariant
            ->expects($this->once())
            ->method('getAppliedPromotionsForChannel')
            ->with($this->channel)
            ->willReturn(new ArrayCollection([$this->createMock(CatalogPromotionInterface::class)]));
        $this->secondUnit->expects($this->once())->method('getOrderItem')->willReturn($this->secondOrderItem);
        $this->firstProductVariant->expects($this->once())->method('getChannelPricingForChannel')->willReturn($this->firstChannelPricing);
        $this->firstChannelPricing->expects($this->once())->method('getMinimumPrice')->willReturn(0);

        $this->promotion->expects($this->once())->method('getName')->willReturn('Test promotion');
        $this->promotion->expects($this->once())->method('getCode')->willReturn('TEST_PROMOTION');
        $this->promotion->expects($this->exactly(2))->method('getAppliesToDiscounted')->willReturn(false);

        $this->adjustmentFactory->expects($this->once())->method('createNew')->willReturn($this->firstPromotionAdjustment);
        $this->firstPromotionAdjustment
            ->expects($this->once())
            ->method('setType')
            ->with(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT);
        $this->firstPromotionAdjustment->expects($this->once())->method('setLabel')->with('Test promotion');
        $this->firstPromotionAdjustment->expects($this->once())->method('setAmount')->with(-100);
        $this->firstPromotionAdjustment->expects($this->once())->method('setOriginCode')->with('TEST_PROMOTION');
        $this->firstUnit->expects($this->once())->method('addAdjustment')->with($this->firstPromotionAdjustment);
        $this->secondUnit->expects($this->never())->method('addAdjustment')->with($this->anything());

        $this->assertTrue(
            $this->command->execute($this->order, ['WEB_US' => ['percentage' => 0.2]], $this->promotion),
        );
    }

    public function testShouldNotApplyDiscountIfAllItemsHaveBeenFilteredOut(): void
    {
        $this->order->expects($this->exactly(2))->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('getCode')->willReturn('WEB_US');
        $this->order->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([$this->firstOrderItem]));
        $this->priceRangeFilter
            ->expects($this->once())
            ->method('filter')
            ->with([$this->firstOrderItem], ['percentage' => 0.2, 'channel' => $this->channel])
            ->willReturn([$this->firstOrderItem]);
        $this->taxonFilter
            ->expects($this->once())
            ->method('filter')
            ->with([$this->firstOrderItem], ['percentage' => 0.2])
            ->willReturn([$this->firstOrderItem]);
        $this->productFilter
            ->expects($this->once())
            ->method('filter')
            ->with([$this->firstOrderItem], ['percentage' => 0.2])
            ->willReturn([]);

        $this->assertFalse(
            $this->command->execute($this->order, ['WEB_US' => ['percentage' => 0.2]], $this->promotion),
        );
    }

    public function testShouldNotApplyDiscountIfConfigurationForOrderChannelIsNotDefined(): void
    {
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('getCode')->willReturn('WEB_PL');
        $this->order->expects($this->never())->method('getItems');

        $this->assertFalse($this->command->execute($this->order, ['WEB_US' => ['percentage' => 0.2]], $this->promotion));
    }

    public function testShouldNotApplyDiscountIfPercentageConfigurationNotDefined(): void
    {
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('getCode')->willReturn('WEB_PL');
        $this->order->expects($this->never())->method('getItems');

        $this->assertFalse($this->command->execute($this->order, ['WEB_US' => []], $this->promotion));
    }

    public function testShouldThrowExceptionIfPassedSubjectIsNotOrder(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->command->execute(
            $this->createMock(PromotionSubjectInterface::class),
            ['percentage' => 0.2],
            $this->promotion,
        );
    }

    public function testShouldRevertsProperPromotionAdjustmentFromAllUnits(): void
    {
        $this->order->expects($this->once())->method('getItems')->willReturn($this->items);
        $this->items->expects($this->once())->method('getIterator')->willReturn(new \ArrayIterator([$this->firstOrderItem]));
        $this->firstOrderItem->expects($this->once())->method('getUnits')->willReturn($this->units);
        $this->units->expects($this->once())->method('getIterator')->willReturn(new \ArrayIterator([$this->firstUnit]));
        $this->firstUnit
            ->expects($this->once())
            ->method('getAdjustments')
            ->with(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)
            ->willReturn(new ArrayCollection([
            $this->firstPromotionAdjustment,
            $this->secondPromotionAdjustment,
        ]));
        $this->promotion->expects($this->exactly(2))->method('getCode')->willReturn('PROMOTION');
        $this->firstPromotionAdjustment->expects($this->once())->method('getOriginCode')->willReturn('PROMOTION');
        $this->firstUnit->expects($this->once())->method('removeAdjustment')->with($this->firstPromotionAdjustment);
        $this->secondPromotionAdjustment->expects($this->once())->method('getOriginCode')->willReturn('OTHER_PROMOTION');

        $this->command->revert($this->order, ['percentage' => 0.2], $this->promotion);
    }

    public function testShouldThrowExceptionIfPassedSubjectToRevertIsNotOrder(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->command->revert(
            $this->createMock(PromotionSubjectInterface::class),
            ['percentage' => 0.2],
            $this->promotion,
        );
    }
}
