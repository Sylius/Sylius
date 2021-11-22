<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Core\Promotion\Applicator;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Distributor\IntegerDistributorInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Promotion\Applicator\UnitsPromotionAdjustmentsApplicatorInterface;
use Sylius\Component\Core\Promotion\Calculator\MinimumPriceBasedPromotionAmountCalculatorInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;

final class UnitsPromotionAdjustmentsApplicatorSpec extends ObjectBehavior
{
    function let(
        AdjustmentFactoryInterface $adjustmentFactory,
        IntegerDistributorInterface $distributor
    ): void {
        $this->beConstructedWith($adjustmentFactory, $distributor);
    }

    function it_implements_an_units_promotion_adjustments_applicator_interface(): void
    {
        $this->shouldImplement(UnitsPromotionAdjustmentsApplicatorInterface::class);
    }

    function it_applies_promotion_adjustments_on_all_units_of_given_order(
        AdjustmentFactoryInterface $adjustmentFactory,
        AdjustmentInterface $firstAdjustment,
        AdjustmentInterface $secondAdjustment,
        AdjustmentInterface $thirdAdjustment,
        IntegerDistributorInterface $distributor,
        OrderInterface $order,
        OrderItemInterface $coltItem,
        OrderItemInterface $magnumItem,
        OrderItemUnitInterface $firstColtUnit,
        OrderItemUnitInterface $magnumUnit,
        OrderItemUnitInterface $secondColtUnit,
        PromotionInterface $promotion,
        ChannelInterface $channel,
        ProductVariantInterface $coltItemVariant,
        ProductVariantInterface $magnumItemVariant,
        ChannelPricingInterface $coltItemChannelPricing,
        ChannelPricingInterface $magnumItemChannelPricing
    ): void {
        $order->countItems()->willReturn(2);
        $order->getChannel()->willReturn($channel);
        $coltItem->getVariant()->willReturn($coltItemVariant);
        $magnumItem->getVariant()->willReturn($magnumItemVariant);
        $coltItemVariant->getChannelPricingForChannel($channel)->willReturn($coltItemChannelPricing);
        $magnumItemVariant->getChannelPricingForChannel($channel)->willReturn($magnumItemChannelPricing);
        $coltItemChannelPricing->getMinimumPrice()->willReturn(0);
        $magnumItemChannelPricing->getMinimumPrice()->willReturn(0);

        $firstColtUnit->getTotal()->willReturn(1000);
        $secondColtUnit->getTotal()->willReturn(1000);
        $magnumUnit->getTotal()->willReturn(2000);

        $order
            ->getItems()
            ->willReturn(new ArrayCollection([$coltItem->getWrappedObject(), $magnumItem->getWrappedObject()]))
        ;

        $coltItem->getQuantity()->willReturn(2);
        $magnumItem->getQuantity()->willReturn(1);

        $distributor->distribute(1000, 2)->willReturn([500, 500]);
        $distributor->distribute(999, 1)->willReturn([999]);

        $coltItem
            ->getUnits()
            ->willReturn(new ArrayCollection([$firstColtUnit->getWrappedObject(), $secondColtUnit->getWrappedObject()]))
        ;
        $magnumItem
            ->getUnits()
            ->willReturn(new ArrayCollection([$magnumUnit->getWrappedObject()]))
        ;

        $promotion->getName()->willReturn('Winter guns promotion!');
        $promotion->getCode()->willReturn('WINTER_GUNS_PROMOTION');

        $adjustmentFactory
            ->createWithData(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT, 'Winter guns promotion!', 500)
            ->willReturn($firstAdjustment, $secondAdjustment)
        ;
        $adjustmentFactory
            ->createWithData(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT, 'Winter guns promotion!', 999)
            ->willReturn($thirdAdjustment)
        ;

        $firstAdjustment->setOriginCode('WINTER_GUNS_PROMOTION')->shouldBeCalled();
        $secondAdjustment->setOriginCode('WINTER_GUNS_PROMOTION')->shouldBeCalled();
        $thirdAdjustment->setOriginCode('WINTER_GUNS_PROMOTION')->shouldBeCalled();

        $firstColtUnit->addAdjustment($firstAdjustment)->shouldBeCalled();
        $secondColtUnit->addAdjustment($secondAdjustment)->shouldBeCalled();
        $magnumUnit->addAdjustment($thirdAdjustment)->shouldBeCalled();

        $this->apply($order, $promotion, [1000, 999]);
    }

    function it_does_not_distribute_0_amount_to_item(
        AdjustmentFactoryInterface $adjustmentFactory,
        AdjustmentInterface $adjustment,
        IntegerDistributorInterface $distributor,
        OrderInterface $order,
        OrderItemInterface $coltItem,
        OrderItemInterface $magnumItem,
        OrderItemUnitInterface $coltUnit,
        OrderItemUnitInterface $magnumUnit,
        PromotionInterface $promotion,
        ChannelInterface $channel,
        ProductVariantInterface $coltItemVariant,
        ProductVariantInterface $magnumItemVariant,
        ChannelPricingInterface $coltItemChannelPricing,
        ChannelPricingInterface $magnumItemChannelPricing
    ): void {
        $order->countItems()->willReturn(2);
        $order->getChannel()->willReturn($channel);
        $coltItem->getVariant()->willReturn($coltItemVariant);
        $magnumItem->getVariant()->willReturn($magnumItemVariant);
        $coltItemVariant->getChannelPricingForChannel($channel)->willReturn($coltItemChannelPricing);
        $magnumItemVariant->getChannelPricingForChannel($channel)->willReturn($magnumItemChannelPricing);
        $coltItemChannelPricing->getMinimumPrice()->willReturn(0);
        $magnumItemChannelPricing->getMinimumPrice()->willReturn(0);

        $coltUnit->getTotal()->willReturn(1000);
        $magnumUnit->getTotal()->willReturn(2000);

        $order
            ->getItems()
            ->willReturn(new ArrayCollection([$coltItem->getWrappedObject(), $magnumItem->getWrappedObject()]))
        ;

        $coltItem->getQuantity()->willReturn(1);
        $magnumItem->getQuantity()->willReturn(1);

        $distributor->distribute(1, 1)->willReturn([1]);

        $coltItem
            ->getUnits()
            ->willReturn(new ArrayCollection([$coltUnit->getWrappedObject()]))
        ;
        $magnumItem
            ->getUnits()
            ->willReturn(new ArrayCollection([$magnumUnit->getWrappedObject()]))
        ;

        $promotion->getName()->willReturn('Winter guns promotion!');
        $promotion->getCode()->willReturn('WINTER_GUNS_PROMOTION');

        $adjustmentFactory
            ->createWithData(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT, 'Winter guns promotion!', 1)
            ->willReturn($adjustment)
        ;

        $adjustment->setOriginCode('WINTER_GUNS_PROMOTION')->shouldBeCalled();

        $coltUnit->addAdjustment($adjustment)->shouldBeCalled();
        $magnumUnit->addAdjustment(Argument::any())->shouldNotBeCalled();

        $this->apply($order, $promotion, [1, 0]);
    }

    function it_does_not_distribute_0_amount_to_item_even_if_its_middle_element(
        AdjustmentFactoryInterface $adjustmentFactory,
        AdjustmentInterface $firstAdjustment,
        AdjustmentInterface $secondAdjustment,
        IntegerDistributorInterface $distributor,
        OrderInterface $order,
        OrderItemInterface $coltItem,
        OrderItemInterface $magnumItem,
        OrderItemInterface $winchesterItem,
        OrderItemUnitInterface $coltUnit,
        OrderItemUnitInterface $magnumUnit,
        OrderItemUnitInterface $winchesterUnit,
        PromotionInterface $promotion,
        ChannelInterface $channel,
        ProductVariantInterface $coltItemVariant,
        ProductVariantInterface $magnumItemVariant,
        ProductVariantInterface $winchesterItemVariant,
        ChannelPricingInterface $coltItemChannelPricing,
        ChannelPricingInterface $magnumItemChannelPricing,
        ChannelPricingInterface $winchesterItemChannelPricing
    ): void {
        $order->countItems()->willReturn(3);
        $order->getChannel()->willReturn($channel);
        $coltItem->getVariant()->willReturn($coltItemVariant);
        $magnumItem->getVariant()->willReturn($magnumItemVariant);
        $winchesterItem->getVariant()->willReturn($winchesterItemVariant);
        $coltItemVariant->getChannelPricingForChannel($channel)->willReturn($coltItemChannelPricing);
        $magnumItemVariant->getChannelPricingForChannel($channel)->willReturn($magnumItemChannelPricing);
        $winchesterItemVariant->getChannelPricingForChannel($channel)->willReturn($winchesterItemChannelPricing);
        $coltItemChannelPricing->getMinimumPrice()->willReturn(0);
        $magnumItemChannelPricing->getMinimumPrice()->willReturn(0);
        $winchesterItemChannelPricing->getMinimumPrice()->willReturn(0);

        $coltUnit->getTotal()->willReturn(1000);
        $magnumUnit->getTotal()->willReturn(2000);
        $winchesterUnit->getTotal()->willReturn(1000);

        $order
            ->getItems()
            ->willReturn(new ArrayCollection([
                $coltItem->getWrappedObject(),
                $magnumItem->getWrappedObject(),
                $winchesterItem->getWrappedObject(),
            ]))
        ;

        $coltItem->getQuantity()->willReturn(1);
        $magnumItem->getQuantity()->willReturn(1);
        $winchesterItem->getQuantity()->willReturn(1);

        $distributor->distribute(1, 1)->willReturn([1]);

        $coltItem
            ->getUnits()
            ->willReturn(new ArrayCollection([$coltUnit->getWrappedObject()]))
        ;
        $magnumItem
            ->getUnits()
            ->willReturn(new ArrayCollection([$magnumUnit->getWrappedObject()]))
        ;
        $winchesterItem
            ->getUnits()
            ->willReturn(new ArrayCollection([$winchesterUnit->getWrappedObject()]))
        ;

        $promotion->getName()->willReturn('Winter guns promotion!');
        $promotion->getCode()->willReturn('WINTER_GUNS_PROMOTION');

        $adjustmentFactory
            ->createWithData(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT, 'Winter guns promotion!', 1)
            ->willReturn($firstAdjustment, $secondAdjustment)
        ;

        $firstAdjustment->setOriginCode('WINTER_GUNS_PROMOTION')->shouldBeCalled();
        $secondAdjustment->setOriginCode('WINTER_GUNS_PROMOTION')->shouldBeCalled();

        $coltUnit->addAdjustment($firstAdjustment)->shouldBeCalled();
        $magnumUnit->addAdjustment(Argument::any())->shouldNotBeCalled();
        $winchesterUnit->addAdjustment($secondAdjustment)->shouldBeCalled();

        $this->apply($order, $promotion, [1, 0, 1]);
    }

    function it_does_not_distribute_0_amount_to_unit(
        AdjustmentFactoryInterface $adjustmentFactory,
        AdjustmentInterface $firstAdjustment,
        AdjustmentInterface $secondAdjustment,
        IntegerDistributorInterface $distributor,
        OrderInterface $order,
        OrderItemInterface $coltItem,
        OrderItemUnitInterface $firstColtUnit,
        OrderItemUnitInterface $secondColtUnit,
        OrderItemUnitInterface $thirdColtUnit,
        PromotionInterface $promotion,
        ChannelInterface $channel,
        ProductVariantInterface $coltItemVariant,
        ChannelPricingInterface $coltItemChannelPricing
    ): void {
        $order->countItems()->willReturn(1);
        $order->getChannel()->willReturn($channel);
        $coltItem->getVariant()->willReturn($coltItemVariant);
        $coltItemVariant->getChannelPricingForChannel($channel)->willReturn($coltItemChannelPricing);
        $coltItemChannelPricing->getMinimumPrice()->willReturn(0);

        $firstColtUnit->getTotal()->willReturn(1000);
        $secondColtUnit->getTotal()->willReturn(1000);
        $thirdColtUnit->getTotal()->willReturn(1000);

        $order
            ->getItems()
            ->willReturn(new ArrayCollection([$coltItem->getWrappedObject()]))
        ;

        $coltItem->getQuantity()->willReturn(3);

        $distributor->distribute(1, 3)->willReturn([1, 0, 1]);

        $coltItem
            ->getUnits()
            ->willReturn(new ArrayCollection([
                $firstColtUnit->getWrappedObject(),
                $secondColtUnit->getWrappedObject(),
                $thirdColtUnit->getWrappedObject(),
            ]))
        ;

        $promotion->getName()->willReturn('Winter guns promotion!');
        $promotion->getCode()->willReturn('WINTER_GUNS_PROMOTION');

        $adjustmentFactory
            ->createWithData(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT, 'Winter guns promotion!', 1)
            ->willReturn($firstAdjustment, $secondAdjustment)
        ;

        $firstAdjustment->setOriginCode('WINTER_GUNS_PROMOTION')->shouldBeCalled();
        $secondAdjustment->setOriginCode('WINTER_GUNS_PROMOTION')->shouldBeCalled();

        $firstColtUnit->addAdjustment($firstAdjustment)->shouldBeCalled();
        $secondColtUnit->addAdjustment(Argument::any())->shouldNotBeCalled();
        $thirdColtUnit->addAdjustment($secondAdjustment)->shouldBeCalled();

        $this->apply($order, $promotion, [1]);
    }

    function it_does_not_distribute_0_amount_to_unit_even_if_its_middle_element(
        AdjustmentFactoryInterface $adjustmentFactory,
        AdjustmentInterface $adjustment,
        IntegerDistributorInterface $distributor,
        OrderInterface $order,
        OrderItemInterface $coltItem,
        OrderItemUnitInterface $firstColtUnit,
        OrderItemUnitInterface $secondColtUnit,
        PromotionInterface $promotion,
        ChannelInterface $channel,
        ProductVariantInterface $coltItemVariant,
        ChannelPricingInterface $coltItemChannelPricing
    ): void {
        $order->countItems()->willReturn(1);
        $order->getChannel()->willReturn($channel);
        $coltItem->getVariant()->willReturn($coltItemVariant);
        $coltItemVariant->getChannelPricingForChannel($channel)->willReturn($coltItemChannelPricing);
        $coltItemChannelPricing->getMinimumPrice()->willReturn(0);

        $firstColtUnit->getTotal()->willReturn(1000);
        $secondColtUnit->getTotal()->willReturn(1000);

        $order
            ->getItems()
            ->willReturn(new ArrayCollection([$coltItem->getWrappedObject()]))
        ;

        $coltItem->getQuantity()->willReturn(2);

        $distributor->distribute(1, 2)->willReturn([1, 0]);

        $coltItem
            ->getUnits()
            ->willReturn(new ArrayCollection([$firstColtUnit->getWrappedObject(), $secondColtUnit->getWrappedObject()]))
        ;

        $promotion->getName()->willReturn('Winter guns promotion!');
        $promotion->getCode()->willReturn('WINTER_GUNS_PROMOTION');

        $adjustmentFactory
            ->createWithData(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT, 'Winter guns promotion!', 1)
            ->willReturn($adjustment)
        ;

        $adjustment->setOriginCode('WINTER_GUNS_PROMOTION')->shouldBeCalled();

        $firstColtUnit->addAdjustment($adjustment)->shouldBeCalled();
        $secondColtUnit->addAdjustment(Argument::any())->shouldNotBeCalled();

        $this->apply($order, $promotion, [1]);
    }

    function it_does_not_apply_promotion_below_product_variant_minimum_price(
        AdjustmentFactoryInterface $adjustmentFactory,
        AdjustmentInterface $firstAdjustment,
        AdjustmentInterface $secondAdjustment,
        AdjustmentInterface $thirdAdjustment,
        IntegerDistributorInterface $distributor,
        OrderInterface $order,
        OrderItemInterface $coltItem,
        OrderItemInterface $magnumItem,
        OrderItemUnitInterface $firstColtUnit,
        OrderItemUnitInterface $magnumUnit,
        OrderItemUnitInterface $secondColtUnit,
        PromotionInterface $promotion,
        ChannelInterface $channel,
        ProductVariantInterface $coltItemVariant,
        ProductVariantInterface $magnumItemVariant,
        ChannelPricingInterface $coltItemChannelPricing,
        ChannelPricingInterface $magnumItemChannelPricing
    ): void {
        $order->countItems()->willReturn(2);
        $order->getChannel()->willReturn($channel);
        $coltItem->getVariant()->willReturn($coltItemVariant);
        $magnumItem->getVariant()->willReturn($magnumItemVariant);
        $coltItemVariant->getChannelPricingForChannel($channel)->willReturn($coltItemChannelPricing);
        $magnumItemVariant->getChannelPricingForChannel($channel)->willReturn($magnumItemChannelPricing);
        $coltItemChannelPricing->getMinimumPrice()->willReturn(500);
        $magnumItemChannelPricing->getMinimumPrice()->willReturn(1900);

        $firstColtUnit->getTotal()->willReturn(1000);
        $secondColtUnit->getTotal()->willReturn(1000);
        $magnumUnit->getTotal()->willReturn(2000);

        $order
            ->getItems()
            ->willReturn(new ArrayCollection([$coltItem->getWrappedObject(), $magnumItem->getWrappedObject()]))
        ;

        $coltItem->getQuantity()->willReturn(2);
        $magnumItem->getQuantity()->willReturn(1);

        $distributor->distribute(1000, 2)->willReturn([-500, -500]);
        $distributor->distribute(999, 1)->willReturn([-999]);

        $coltItem
            ->getUnits()
            ->willReturn(new ArrayCollection([$firstColtUnit->getWrappedObject(), $secondColtUnit->getWrappedObject()]))
        ;
        $magnumItem
            ->getUnits()
            ->willReturn(new ArrayCollection([$magnumUnit->getWrappedObject()]))
        ;

        $promotion->getName()->willReturn('Winter guns promotion!');
        $promotion->getCode()->willReturn('WINTER_GUNS_PROMOTION');

        $adjustmentFactory
            ->createWithData(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT, 'Winter guns promotion!', -500)
            ->willReturn($firstAdjustment, $secondAdjustment)
        ;
        $adjustmentFactory
            ->createWithData(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT, 'Winter guns promotion!', -100)
            ->willReturn($thirdAdjustment)
        ;

        $firstAdjustment->setOriginCode('WINTER_GUNS_PROMOTION')->shouldBeCalled();
        $secondAdjustment->setOriginCode('WINTER_GUNS_PROMOTION')->shouldBeCalled();
        $thirdAdjustment->setOriginCode('WINTER_GUNS_PROMOTION')->shouldBeCalled();

        $firstColtUnit->addAdjustment($firstAdjustment)->shouldBeCalled();
        $secondColtUnit->addAdjustment($secondAdjustment)->shouldBeCalled();
        $magnumUnit->addAdjustment($thirdAdjustment)->shouldBeCalled();

        $this->apply($order, $promotion, [1000, 999]);
    }

    function it_throws_exception_if_items_count_is_different_than_adjustment_amounts(
        PromotionInterface $promotion,
        OrderInterface $order
    ): void {
        $order->countItems()->willReturn(2);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('apply', [$order, $promotion, [1999]])
        ;
    }
}
