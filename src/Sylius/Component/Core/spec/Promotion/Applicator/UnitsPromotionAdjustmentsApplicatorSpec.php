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
use Sylius\Component\Originator\Originator\OriginatorInterface;
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
        IntegerDistributorInterface $distributor,
        OriginatorInterface $originator
    ) {
        $this->beConstructedWith($adjustmentFactory, $distributor, $originator);
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
        OriginatorInterface $originator,
        PromotionInterface $promotion
    ) {
        $order->countItems()->willReturn(2);

        $order
            ->getItems()
            ->willReturn(new \ArrayIterator([$coltItem->getWrappedObject(), $magnumItem->getWrappedObject()]))
        ;

        $coltItem->getQuantity()->willReturn(2);
        $magnumItem->getQuantity()->willReturn(1);

        $distributor->distribute(1000, 2)->willReturn([500, 500]);
        $distributor->distribute(999, 1)->willReturn([999]);

        $coltItem
            ->getUnits()
            ->willReturn(new \ArrayIterator([$firstColtUnit->getWrappedObject(), $secondColtUnit->getWrappedObject()]))
        ;
        $magnumItem
            ->getUnits()
            ->willReturn(new \ArrayIterator([$magnumUnit->getWrappedObject()]))
        ;

        $promotion->getName()->willReturn('Winter guns promotion!');

        $adjustmentFactory
            ->createWithData(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT, 'Winter guns promotion!', 500)
            ->willReturn($firstAdjustment, $secondAdjustment)
        ;
        $adjustmentFactory
            ->createWithData(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT, 'Winter guns promotion!', 999)
            ->willReturn($thirdAdjustment)
        ;

        $originator->setOrigin($firstAdjustment, $promotion)->shouldBeCalled();
        $originator->setOrigin($secondAdjustment, $promotion)->shouldBeCalled();
        $originator->setOrigin($thirdAdjustment, $promotion)->shouldBeCalled();

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
        OriginatorInterface $originator,
        PromotionInterface $promotion
    ) {
        $order->countItems()->willReturn(2);

        $order
            ->getItems()
            ->willReturn(new \ArrayIterator([$coltItem->getWrappedObject(), $magnumItem->getWrappedObject()]))
        ;

        $coltItem->getQuantity()->willReturn(1);
        $magnumItem->getQuantity()->willReturn(1);

        $distributor->distribute(1, 1)->willReturn([1]);

        $coltItem
            ->getUnits()
            ->willReturn(new \ArrayIterator([$coltUnit->getWrappedObject()]))
        ;
        $magnumItem
            ->getUnits()
            ->willReturn(new \ArrayIterator([$magnumUnit->getWrappedObject()]))
        ;

        $promotion->getName()->willReturn('Winter guns promotion!');

        $adjustmentFactory
            ->createWithData(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT, 'Winter guns promotion!', 1)
            ->willReturn($adjustment)
        ;

        $originator->setOrigin($adjustment, $promotion)->shouldBeCalled();

        $coltUnit->addAdjustment($adjustment)->shouldBeCalled();
        $magnumUnit->addAdjustment(Argument::any())->shouldNotBeCalled();

        $this->apply($order, $promotion, [1, 0]);
    }

    function it_does_not_distribute_0_amount_to_unit(
        AdjustmentFactoryInterface $adjustmentFactory,
        AdjustmentInterface $adjustment,
        IntegerDistributorInterface $distributor,
        OrderInterface $order,
        OrderItemInterface $coltItem,
        OrderItemUnitInterface $firstColtUnit,
        OrderItemUnitInterface $secondColtUnit,
        OriginatorInterface $originator,
        PromotionInterface $promotion
    ) {
        $order->countItems()->willReturn(1);

        $order
            ->getItems()
            ->willReturn(new \ArrayIterator([$coltItem->getWrappedObject()]))
        ;

        $coltItem->getQuantity()->willReturn(2);

        $distributor->distribute(1, 2)->willReturn([1, 0]);

        $coltItem
            ->getUnits()
            ->willReturn(new \ArrayIterator([$firstColtUnit->getWrappedObject(), $secondColtUnit->getWrappedObject()]))
        ;

        $promotion->getName()->willReturn('Winter guns promotion!');

        $adjustmentFactory
            ->createWithData(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT, 'Winter guns promotion!', 1)
            ->willReturn($adjustment)
        ;

        $originator->setOrigin($adjustment, $promotion)->shouldBeCalled();

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
            ->shouldThrow(new \InvalidArgumentException(
                'Number of adjustments amount to distribute must be equal with number of order items.'
            ))
            ->during('apply', [$order, $promotion, [1999]])
        ;
    }
}
