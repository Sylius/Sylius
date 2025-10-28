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

namespace Tests\Sylius\Component\Core\Promotion\Applicator;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Distributor\IntegerDistributorInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Promotion\Applicator\UnitsPromotionAdjustmentsApplicator;
use Sylius\Component\Core\Promotion\Applicator\UnitsPromotionAdjustmentsApplicatorInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;

final class UnitsPromotionAdjustmentsApplicatorTest extends TestCase
{
    private AdjustmentFactoryInterface&MockObject $adjustmentFactory;

    private IntegerDistributorInterface&MockObject $distributor;

    private AdjustmentInterface&MockObject $firstAdjustment;

    private AdjustmentInterface&MockObject $secondAdjustment;

    private AdjustmentInterface&MockObject $thirdAdjustment;

    private MockObject&OrderInterface $order;

    private MockObject&OrderItemInterface $coltItem;

    private MockObject&OrderItemInterface $magnumItem;

    private MockObject&OrderItemUnitInterface $firstColtUnit;

    private MockObject&OrderItemUnitInterface $magnumUnit;

    private MockObject&OrderItemUnitInterface $secondColtUnit;

    private MockObject&OrderItemUnitInterface $thirdColtUnit;

    private MockObject&PromotionInterface $promotion;

    private ChannelInterface&MockObject $channel;

    private MockObject&ProductVariantInterface $coltItemVariant;

    private MockObject&ProductVariantInterface $magnumItemVariant;

    private ChannelPricingInterface&MockObject $coltItemChannelPricing;

    private ChannelPricingInterface&MockObject $magnumItemChannelPricing;

    private UnitsPromotionAdjustmentsApplicator $applicator;

    protected function setUp(): void
    {
        $this->adjustmentFactory = $this->createMock(AdjustmentFactoryInterface::class);
        $this->distributor = $this->createMock(IntegerDistributorInterface::class);
        $this->firstAdjustment = $this->createMock(AdjustmentInterface::class);
        $this->secondAdjustment = $this->createMock(AdjustmentInterface::class);
        $this->thirdAdjustment = $this->createMock(AdjustmentInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
        $this->coltItem = $this->createMock(OrderItemInterface::class);
        $this->magnumItem = $this->createMock(OrderItemInterface::class);
        $this->firstColtUnit = $this->createMock(OrderItemUnitInterface::class);
        $this->magnumUnit = $this->createMock(OrderItemUnitInterface::class);
        $this->secondColtUnit = $this->createMock(OrderItemUnitInterface::class);
        $this->thirdColtUnit = $this->createMock(OrderItemUnitInterface::class);
        $this->promotion = $this->createMock(PromotionInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->coltItem = $this->createMock(OrderItemInterface::class);
        $this->magnumItem = $this->createMock(OrderItemInterface::class);
        $this->coltItemVariant = $this->createMock(ProductVariantInterface::class);
        $this->magnumItemVariant = $this->createMock(ProductVariantInterface::class);
        $this->coltItemChannelPricing = $this->createMock(ChannelPricingInterface::class);
        $this->magnumItemChannelPricing = $this->createMock(ChannelPricingInterface::class);
        $this->applicator = new UnitsPromotionAdjustmentsApplicator(
            $this->adjustmentFactory,
            $this->distributor,
        );
    }

    public function testShouldImplementUnitsPromotionAdjustmentsApplicatorInterface(): void
    {
        $this->assertInstanceOf(UnitsPromotionAdjustmentsApplicatorInterface::class, $this->applicator);
    }

    public function testShouldApplyPromotionAdjustmentsOnAllUnitsOfGivenOrder(): void
    {
        $this->order->expects($this->once())->method('countItems')->willReturn(2);
        $this->order->expects($this->exactly(2))->method('getChannel')->willReturn($this->channel);
        $this->coltItem->expects($this->once())->method('getVariant')->willReturn($this->coltItemVariant);
        $this->magnumItem->expects($this->once())->method('getVariant')->willReturn($this->magnumItemVariant);
        $this->coltItemVariant->expects($this->once())
            ->method('getChannelPricingForChannel')
            ->with($this->channel)
            ->willReturn($this->coltItemChannelPricing);
        $this->magnumItemVariant->expects($this->once())
            ->method('getChannelPricingForChannel')
            ->with($this->channel)
            ->willReturn($this->magnumItemChannelPricing);
        $this->coltItemChannelPricing->expects($this->once())->method('getMinimumPrice')->willReturn(0);
        $this->magnumItemChannelPricing->expects($this->once())->method('getMinimumPrice')->willReturn(0);
        $this->firstColtUnit->expects($this->once())->method('getTotal')->willReturn(1000);
        $this->secondColtUnit->expects($this->once())->method('getTotal')->willReturn(1000);
        $this->magnumUnit->expects($this->once())->method('getTotal')->willReturn(2000);
        $this->order->expects($this->once())
            ->method('getItems')
            ->willReturn(new ArrayCollection([
                $this->coltItem,
                $this->magnumItem,
            ]));
        $this->coltItem->expects($this->once())->method('getQuantity')->willReturn(2);
        $this->magnumItem->expects($this->once())->method('getQuantity')->willReturn(1);
        $this->distributor->expects($this->exactly(2))
            ->method('distribute')
            ->willReturnMap([
                [1000.0, 2, [500, 500]],
                [999.0, 1, [999]],
            ]);
        $this->coltItem->expects($this->once())
            ->method('getUnits')
            ->willReturn(new ArrayCollection([
                $this->firstColtUnit,
                $this->secondColtUnit,
            ]));
        $this->magnumItem->expects($this->once())
            ->method('getUnits')
            ->willReturn(new ArrayCollection([
                $this->magnumUnit,
            ]));
        $this->promotion->expects($this->exactly(3))->method('getName')->willReturn('Winter guns promotion!');
        $this->promotion->expects($this->exactly(3))->method('getCode')->willReturn('WINTER_GUNS_PROMOTION');

        $this->adjustmentFactory->expects($this->exactly(3))
            ->method('createWithData')
            ->willReturnMap([
                [AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT, 'Winter guns promotion!', 500, $this->firstAdjustment],
                [AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT, 'Winter guns promotion!', 500, $this->secondAdjustment],
                [AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT, 'Winter guns promotion!', 999, $this->thirdAdjustment],
            ]);
        $this->firstAdjustment->expects($this->exactly(2))->method('setOriginCode')->with('WINTER_GUNS_PROMOTION');
        $this->thirdAdjustment->expects($this->once())->method('setOriginCode')->with('WINTER_GUNS_PROMOTION');
        $this->firstColtUnit->expects($this->once())->method('addAdjustment')->with($this->firstAdjustment);
        $this->secondColtUnit->expects($this->once())->method('addAdjustment')->with($this->secondAdjustment);
        $this->magnumUnit->expects($this->once())->method('addAdjustment')->with($this->thirdAdjustment);

        $this->applicator->apply($this->order, $this->promotion, [1000, 999]);
    }

    public function testShouldNotDistributeZeroAmountItem(): void
    {
        $this->order->expects($this->once())->method('countItems')->willReturn(2);
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->order->expects($this->once())
            ->method('getItems')
            ->willReturn(new ArrayCollection([
                $this->coltItem,
                $this->magnumItem,
            ]));
        $this->coltItem->expects($this->once())->method('getVariant')->willReturn($this->coltItemVariant);
        $this->coltItemVariant->expects($this->once())
            ->method('getChannelPricingForChannel')
            ->with($this->channel)
            ->willReturn($this->coltItemChannelPricing);
        $this->coltItemChannelPricing->expects($this->once())->method('getMinimumPrice')->willReturn(0);
        $this->firstColtUnit->expects($this->once())->method('getTotal')->willReturn(1000);
        $this->coltItem->expects($this->once())->method('getQuantity')->willReturn(1);
        $this->distributor->expects($this->once())
            ->method('distribute')
            ->with(1, 1)
            ->willReturn([1]);
        $this->coltItem->expects($this->once())
            ->method('getUnits')
            ->willReturn(new ArrayCollection([
                $this->firstColtUnit,
            ]));
        $this->promotion->expects($this->once())->method('getName')->willReturn('Winter guns promotion!');
        $this->promotion->expects($this->once())->method('getCode')->willReturn('WINTER_GUNS_PROMOTION');
        $this->adjustmentFactory->expects($this->once())
            ->method('createWithData')
            ->with(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT, 'Winter guns promotion!', 1)
            ->willReturn($this->firstAdjustment);
        $this->firstAdjustment->expects($this->once())
            ->method('setOriginCode')
            ->with('WINTER_GUNS_PROMOTION');
        $this->firstColtUnit->expects($this->once())
            ->method('addAdjustment')
            ->with($this->firstAdjustment);
        $this->magnumUnit->expects($this->never())
            ->method('addAdjustment');

        $this->applicator->apply($this->order, $this->promotion, [1, 0]);
    }

    public function testShouldNotDistributeZeroAmountToItemEvenIfItsMiddleElement(): void
    {
        $winchesterItem = $this->createMock(OrderItemInterface::class);
        $winchesterUnit = $this->createMock(OrderItemUnitInterface::class);
        $winchesterItemVariant = $this->createMock(ProductVariantInterface::class);
        $winchesterItemChannelPricing = $this->createMock(ChannelPricingInterface::class);
        $this->order->expects($this->once())->method('countItems')->willReturn(3);
        $this->order->expects($this->atLeastOnce())->method('getChannel')->willReturn($this->channel);
        $this->coltItem->expects($this->once())->method('getVariant')->willReturn($this->coltItemVariant);
        $winchesterItem->expects($this->once())->method('getVariant')->willReturn($winchesterItemVariant);
        $this->coltItemVariant
            ->expects($this->once())
            ->method('getChannelPricingForChannel')
            ->with($this->channel)
            ->willReturn($this->coltItemChannelPricing);
        $winchesterItemVariant
            ->expects($this->once())
            ->method('getChannelPricingForChannel')
            ->with($this->channel)
            ->willReturn($winchesterItemChannelPricing);
        $this->coltItemChannelPricing->expects($this->once())->method('getMinimumPrice')->willReturn(0);
        $winchesterItemChannelPricing->expects($this->once())->method('getMinimumPrice')->willReturn(0);
        $this->firstColtUnit->expects($this->once())->method('getTotal')->willReturn(1000);
        $winchesterUnit->expects($this->once())->method('getTotal')->willReturn(1000);
        $this->order->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([
                $this->coltItem,
                $this->magnumItem,
                $winchesterItem,
        ]));
        $this->coltItem->expects($this->once())->method('getQuantity')->willReturn(1);
        $winchesterItem->expects($this->once())->method('getQuantity')->willReturn(1);
        $this->distributor->expects($this->exactly(2))->method('distribute')->willReturnMap([
            [1.0, 1, [1]],
            [1.0, 1, [1]],
        ]);
        $this->coltItem->expects($this->once())->method('getUnits')->willReturn(new ArrayCollection([$this->firstColtUnit]));
        $winchesterItem->expects($this->once())->method('getUnits')->willReturn(new ArrayCollection([$winchesterUnit]));
        $this->promotion->expects($this->atLeastOnce())->method('getName')->willReturn('Winter guns promotion!');
        $this->promotion->expects($this->atLeastOnce())->method('getCode')->willReturn('WINTER_GUNS_PROMOTION');

        $this->adjustmentFactory->expects($this->exactly(2))
            ->method('createWithData')
            ->willReturnMap([
                [AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT, 'Winter guns promotion!', 1, $this->firstAdjustment],
                [AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT, 'Winter guns promotion!', 1, $this->secondAdjustment],
            ]);
        $this->firstAdjustment->expects($this->atLeastOnce())->method('setOriginCode')->with('WINTER_GUNS_PROMOTION');
        $this->firstColtUnit->expects($this->once())->method('addAdjustment')->with($this->firstAdjustment);
        $this->magnumUnit->expects($this->never())->method('addAdjustment');
        $winchesterUnit->expects($this->once())->method('addAdjustment')->with($this->secondAdjustment);

        $this->applicator->apply($this->order, $this->promotion, [1, 0, 1]);
    }

    public function testShouldNotDistributeZeroAmountToUnit(): void
    {
        $this->order->expects($this->once())->method('countItems')->willReturn(1);
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->coltItem->expects($this->once())->method('getVariant')->willReturn($this->coltItemVariant);
        $this->coltItemVariant
            ->expects($this->once())
            ->method('getChannelPricingForChannel')
            ->with($this->channel)
            ->willReturn($this->coltItemChannelPricing);
        $this->coltItemChannelPricing->expects($this->once())->method('getMinimumPrice')->willReturn(0);
        $this->firstColtUnit->expects($this->once())->method('getTotal')->willReturn(1000);
        $this->firstColtUnit->expects($this->atLeastOnce())->method('getAdjustmentsTotal')->willReturn(0);
        $this->secondColtUnit->expects($this->atLeastOnce())->method('getAdjustmentsTotal')->willReturn(10);
        $this->thirdColtUnit->expects($this->once())->method('getTotal')->willReturn(1000);
        $this->thirdColtUnit->expects($this->atLeastOnce())->method('getAdjustmentsTotal')->willReturn(10);
        $this->order->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([$this->coltItem]));
        $this->coltItem->expects($this->once())->method('getQuantity')->willReturn(3);
        $this->distributor->expects($this->once())->method('distribute')->with(1, 3)->willReturn([1, 0, 1]);
        $this->coltItem->expects($this->once())->method('getUnits')->willReturn(new ArrayCollection([
            $this->firstColtUnit, $this->secondColtUnit, $this->thirdColtUnit,
        ]));
        $this->promotion->expects($this->exactly(2))->method('getName')->willReturn('Winter guns promotion!');
        $this->promotion->expects($this->exactly(2))->method('getCode')->willReturn('WINTER_GUNS_PROMOTION');
        $this->adjustmentFactory->expects($this->exactly(2))->method('createWithData')->willReturnMap([
            [AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT, 'Winter guns promotion!', 1, $this->firstAdjustment],
            [AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT, 'Winter guns promotion!', 1, $this->secondAdjustment],
        ]);
        $this->firstAdjustment->expects($this->exactly(2))->method('setOriginCode')->with('WINTER_GUNS_PROMOTION');
        $this->firstColtUnit->expects($this->once())->method('addAdjustment')->with($this->firstAdjustment);
        $this->secondColtUnit->expects($this->never())->method('addAdjustment');
        $this->thirdColtUnit->expects($this->once())->method('addAdjustment')->with($this->secondAdjustment);

        $this->applicator->apply($this->order, $this->promotion, [1]);
    }

    public function testShouldNotDistributeZeroAmountToUnitEvenIfItsMiddleElement(): void
    {
        $this->order->expects($this->once())->method('countItems')->willReturn(1);
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->coltItem->expects($this->once())->method('getVariant')->willReturn($this->coltItemVariant);
        $this->coltItemVariant
            ->expects($this->once())
            ->method('getChannelPricingForChannel')
            ->with($this->channel)
            ->willReturn($this->coltItemChannelPricing);
        $this->coltItemChannelPricing->expects($this->once())->method('getMinimumPrice')->willReturn(0);
        $this->firstColtUnit->expects($this->once())->method('getTotal')->willReturn(1000);
        $this->firstColtUnit->expects($this->once())->method('getAdjustmentsTotal')->willReturn(0);
        $this->secondColtUnit->expects($this->once())->method('getAdjustmentsTotal')->willReturn(10);
        $this->order->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([$this->coltItem]));
        $this->coltItem->expects($this->once())->method('getQuantity')->willReturn(2);
        $this->distributor->expects($this->once())->method('distribute')->with(1, 2)->willReturn([1, 0]);
        $this->coltItem->expects($this->once())->method('getUnits')->willReturn(new ArrayCollection([
            $this->firstColtUnit, $this->secondColtUnit,
        ]));
        $this->promotion->expects($this->once())->method('getName')->willReturn('Winter guns promotion!');
        $this->promotion->expects($this->once())->method('getCode')->willReturn('WINTER_GUNS_PROMOTION');
        $this->adjustmentFactory->expects($this->once())->method('createWithData')->with(
            AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT,
            'Winter guns promotion!',
            1,
        )->willReturn($this->firstAdjustment);
        $this->firstAdjustment->expects($this->once())->method('setOriginCode')->with('WINTER_GUNS_PROMOTION');
        $this->firstColtUnit->expects($this->once())->method('addAdjustment')->with($this->firstAdjustment);
        $this->secondColtUnit->expects($this->never())->method('addAdjustment');

        $this->applicator->apply($this->order, $this->promotion, [1]);
    }

    public function testShouldNotApplyPromotionBelowProductVariantMinimumPrice(): void
    {
        $this->order->expects($this->once())->method('countItems')->willReturn(2);
        $this->order->expects($this->exactly(2))->method('getChannel')->willReturn($this->channel);
        $this->coltItem->expects($this->once())->method('getVariant')->willReturn($this->coltItemVariant);
        $this->magnumItem->expects($this->once())->method('getVariant')->willReturn($this->magnumItemVariant);
        $this->coltItemVariant
            ->expects($this->once())
            ->method('getChannelPricingForChannel')
            ->with($this->channel)
            ->willReturn($this->coltItemChannelPricing);
        $this->magnumItemVariant
            ->expects($this->once())
            ->method('getChannelPricingForChannel')
            ->with($this->channel)
            ->willReturn($this->magnumItemChannelPricing);
        $this->coltItemChannelPricing->expects($this->once())->method('getMinimumPrice')->willReturn(500);
        $this->magnumItemChannelPricing->expects($this->once())->method('getMinimumPrice')->willReturn(1900);
        $this->firstColtUnit->expects($this->once())->method('getTotal')->willReturn(1000);
        $this->secondColtUnit->expects($this->once())->method('getTotal')->willReturn(1000);
        $this->magnumUnit->expects($this->once())->method('getTotal')->willReturn(2000);
        $this->order->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([
            $this->coltItem, $this->magnumItem,
        ]));
        $this->coltItem->expects($this->once())->method('getQuantity')->willReturn(2);
        $this->magnumItem->expects($this->once())->method('getQuantity')->willReturn(1);
        $this->distributor->expects($this->exactly(2))->method('distribute')->willReturnMap([
            [1000.0, 2, [-500, -500]],
            [999.0, 1, [-999]],
        ]);
        $this->coltItem->expects($this->once())->method('getUnits')->willReturn(new ArrayCollection([
            $this->firstColtUnit, $this->secondColtUnit,
        ]));
        $this->magnumItem->expects($this->once())->method('getUnits')->willReturn(new ArrayCollection([
            $this->magnumUnit,
        ]));
        $this->promotion->expects($this->exactly(3))->method('getName')->willReturn('Winter guns promotion!');
        $this->promotion->expects($this->exactly(3))->method('getCode')->willReturn('WINTER_GUNS_PROMOTION');
        $this->adjustmentFactory->expects($this->exactly(3))->method('createWithData')->willReturnMap([
            [AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT, 'Winter guns promotion!', -500, $this->firstAdjustment],
            [AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT, 'Winter guns promotion!', -500, $this->secondAdjustment],
            [AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT, 'Winter guns promotion!', -100, $this->thirdAdjustment],
        ]);
        $this->firstAdjustment->expects($this->exactly(2))->method('setOriginCode')->with('WINTER_GUNS_PROMOTION');
        $this->thirdAdjustment->expects($this->once())->method('setOriginCode')->with('WINTER_GUNS_PROMOTION');
        $this->firstColtUnit->expects($this->once())->method('addAdjustment')->with($this->firstAdjustment);
        $this->secondColtUnit->expects($this->once())->method('addAdjustment')->with($this->secondAdjustment);
        $this->magnumUnit->expects($this->once())->method('addAdjustment')->with($this->thirdAdjustment);

        $this->applicator->apply($this->order, $this->promotion, [1000, 999]);
    }

    public function testShouldThrowExceptionIfItemsCountIsDifferentThanAdjustmentsAmount(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->order->expects($this->once())->method('countItems')->willReturn(2);

        $this->applicator->apply($this->order, $this->promotion, [1999]);
    }
}
