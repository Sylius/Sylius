<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Promotion\Applicator;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Distributor\IntegerDistributorInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Promotion\Applicator\UnitsPromotionAdjustmentsApplicator;
use Sylius\Component\Core\Promotion\Applicator\UnitsPromotionAdjustmentsApplicatorInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;

/**
 * @mixin UnitsPromotionAdjustmentsApplicator
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class UnitsPromotionAdjustmentsApplicatorSpec extends ObjectBehavior
{
    function let(
        AdjustmentFactoryInterface $adjustmentFactory,
        IntegerDistributorInterface $distributor
    ) {
        $this->beConstructedWith($adjustmentFactory, $distributor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Promotion\Applicator\UnitsPromotionAdjustmentsApplicator');
    }

    function it_implements_units_promotion_adjustments_applicator_interface()
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
        PromotionInterface $promotion
    ) {
        $order->countItems()->willReturn(2);

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
        PromotionInterface $promotion
    ) {
        $order->countItems()->willReturn(2);

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
        PromotionInterface $promotion
    ) {
        $order->countItems()->willReturn(3);

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
        PromotionInterface $promotion
    ) {
        $order->countItems()->willReturn(1);

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
                $thirdColtUnit->getWrappedObject()
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
        PromotionInterface $promotion
    ) {
        $order->countItems()->willReturn(1);

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

    function it_throws_exception_if_items_count_is_different_than_adjustment_amounts(
        PromotionInterface $promotion,
        OrderInterface $order
    ) {
        $order->countItems()->willReturn(2);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('apply', [$order, $promotion, [1999]])
        ;
    }
}
