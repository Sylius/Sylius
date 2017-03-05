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
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Promotion\Applicator\OrderItemPromotionAdjustmentsApplicator;
use Sylius\Component\Core\Promotion\Applicator\OrderItemPromotionAdjustmentsApplicatorInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Gorka Laucirica <gorka.lauzirika@gmail.com>
 */
final class OrderItemPromotionAdjustmentsApplicatorSpec extends ObjectBehavior
{
    function let(
        AdjustmentFactoryInterface $adjustmentFactory
    ) {
        $this->beConstructedWith($adjustmentFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OrderItemPromotionAdjustmentsApplicator::class);
    }

    function it_implements_an_units_promotion_adjustments_applicator_interface()
    {
        $this->shouldImplement(OrderItemPromotionAdjustmentsApplicatorInterface::class);
    }

    function it_applies_promotion_adjustments_on_all_units_of_given_order_item(
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $unit1,
        OrderItemUnitInterface $unit2,
        PromotionInterface $promotion,
        AdjustmentFactoryInterface $adjustmentFactory,
        AdjustmentInterface $firstAdjustment,
        AdjustmentInterface $secondAdjustment
    ) {
        $promoName = 'Winter guns promotion!';
        $promoCode = 'WINTER_GUNS_PROMOTION';
        $orderItem
            ->getUnits()
            ->willReturn(new ArrayCollection([$unit1->getWrappedObject(), $unit2->getWrappedObject()]))
        ;

        $promotion->getName()->willReturn($promoName);
        $promotion->getCode()->willReturn($promoCode);

        $adjustmentFactory
            ->createNew()
            ->willReturn($firstAdjustment, $secondAdjustment)
        ;

        $firstAdjustment->setType(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $firstAdjustment->setLabel($promoName)->shouldBeCalled();
        $firstAdjustment->setOriginCode($promoCode)->shouldBeCalled();
        $firstAdjustment->setAmount(-1000)->shouldBeCalled();

        $secondAdjustment->setType(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $secondAdjustment->setLabel($promoName)->shouldBeCalled();
        $secondAdjustment->setOriginCode($promoCode)->shouldBeCalled();
        $secondAdjustment->setAmount(-1000)->shouldBeCalled();

        $unit1->addAdjustment($firstAdjustment)->shouldBeCalled();
        $unit1->getTotal()->shouldBeCalled()->willReturn(5000);
        $unit2->addAdjustment($secondAdjustment)->shouldBeCalled();
        $unit2->getTotal()->shouldBeCalled()->willReturn(5000);

        $this->apply($orderItem, $promotion, 1000);
    }


    function it_prevents_appliying_an_adjustment_bigger_than_unit_total(
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $unit1,
        OrderItemUnitInterface $unit2,
        PromotionInterface $promotion,
        AdjustmentFactoryInterface $adjustmentFactory,
        AdjustmentInterface $firstAdjustment,
        AdjustmentInterface $secondAdjustment
    ) {
        $promoName = 'Winter guns promotion!';
        $promoCode = 'WINTER_GUNS_PROMOTION';
        $orderItem
            ->getUnits()
            ->willReturn(new ArrayCollection([$unit1->getWrappedObject(), $unit2->getWrappedObject()]))
        ;

        $promotion->getName()->willReturn($promoName);
        $promotion->getCode()->willReturn($promoCode);

        $adjustmentFactory
            ->createNew()
            ->willReturn($firstAdjustment, $secondAdjustment)
        ;

        $firstAdjustment->setType(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $firstAdjustment->setLabel($promoName)->shouldBeCalled();
        $firstAdjustment->setOriginCode($promoCode)->shouldBeCalled();
        $firstAdjustment->setAmount(-500)->shouldBeCalled();

        $secondAdjustment->setType(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $secondAdjustment->setLabel($promoName)->shouldBeCalled();
        $secondAdjustment->setOriginCode($promoCode)->shouldBeCalled();
        $secondAdjustment->setAmount(-500)->shouldBeCalled();

        $unit1->addAdjustment($firstAdjustment)->shouldBeCalled();
        $unit1->getTotal()->shouldBeCalled()->willReturn(500);
        $unit2->addAdjustment($secondAdjustment)->shouldBeCalled();
        $unit2->getTotal()->shouldBeCalled()->willReturn(500);

        $this->apply($orderItem, $promotion, 1000);
    }
}
