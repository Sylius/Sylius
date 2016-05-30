<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Promotion\Action;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Distributor\IntegerDistributorInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Promotion\Action\UnitDiscountAction;
use Sylius\Component\Core\Promotion\Filter\FilterInterface;
use Sylius\Component\Originator\Originator\OriginatorInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class UnitPercentageDiscountActionSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $adjustmentFactory,
        OriginatorInterface $originator,
        IntegerDistributorInterface $distributor,
        FilterInterface $priceRangeFilter,
        FilterInterface $taxonFilter
    ) {
        $this->beConstructedWith($adjustmentFactory, $originator, $distributor, $priceRangeFilter, $taxonFilter);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Promotion\Action\UnitPercentageDiscountAction');
    }

    function it_is_item_discount_action()
    {
        $this->shouldHaveType(UnitDiscountAction::class);
    }

    function it_applies_percentage_discount_on_every_unit_in_order(
        $adjustmentFactory,
        $distributor,
        $originator,
        $priceRangeFilter,
        $taxonFilter,
        AdjustmentInterface $promotionAdjustment1,
        AdjustmentInterface $promotionAdjustment2,
        Collection $originalItems,
        Collection $units,
        OrderInterface $order,
        OrderItemInterface $orderItem1,
        OrderItemInterface $orderItem2,
        OrderItemInterface $orderItem3,
        OrderItemUnitInterface $unit1,
        OrderItemUnitInterface $unit2,
        PromotionInterface $promotion
    ) {
        $order->getItems()->willReturn($originalItems);
        $originalItems->toArray()->willReturn([$orderItem1, $orderItem2, $orderItem3]);

        $priceRangeFilter
            ->filter([$orderItem1, $orderItem2, $orderItem3], ['percentage' => 0.2, 'filters' => ['taxons' => ['testTaxon']]])
            ->willReturn([$orderItem1, $orderItem2])
        ;
        $taxonFilter
            ->filter([$orderItem1, $orderItem2], ['percentage' => 0.2, 'filters' => ['taxons' => ['testTaxon']]])
            ->willReturn([$orderItem1])
        ;

        $orderItem1->getQuantity()->willReturn(2);
        $orderItem1->getUnits()->willReturn($units);
        $units->getIterator()->willReturn(new \ArrayIterator([$unit1->getWrappedObject(), $unit2->getWrappedObject()]));

        $orderItem1->getTotal()->willReturn(1000);
        $distributor->distribute(200, 2)->willReturn([100, 100]);

        $promotion->getName()->willReturn('Test promotion');

        $adjustmentFactory->createNew()->willReturn($promotionAdjustment1, $promotionAdjustment2);

        $promotionAdjustment1->setType(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $promotionAdjustment1->setLabel('Test promotion')->shouldBeCalled();
        $promotionAdjustment1->setAmount(-100)->shouldBeCalled();

        $originator->setOrigin($promotionAdjustment1, $promotion)->shouldBeCalled();

        $promotionAdjustment2->setType(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $promotionAdjustment2->setLabel('Test promotion')->shouldBeCalled();
        $promotionAdjustment2->setAmount(-100)->shouldBeCalled();

        $originator->setOrigin($promotionAdjustment2, $promotion)->shouldBeCalled();

        $unit1->addAdjustment($promotionAdjustment1)->shouldBeCalled();
        $unit2->addAdjustment($promotionAdjustment2)->shouldBeCalled();

        $this->execute($order, ['percentage' => 0.2, 'filters' => ['taxons' => ['testTaxon']]], $promotion);
    }

    function it_does_not_apply_promotions_with_amount_0(
        $adjustmentFactory,
        $distributor,
        $originator,
        $priceRangeFilter,
        $taxonFilter,
        AdjustmentInterface $promotionAdjustment1,
        Collection $originalItems,
        Collection $units,
        OrderInterface $order,
        OrderItemInterface $orderItem1,
        OrderItemInterface $orderItem2,
        OrderItemInterface $orderItem3,
        OrderItemUnitInterface $unit1,
        OrderItemUnitInterface $unit2,
        PromotionInterface $promotion
    ) {
        $order->getItems()->willReturn($originalItems);
        $originalItems->toArray()->willReturn([$orderItem1, $orderItem2, $orderItem3]);

        $priceRangeFilter
            ->filter([$orderItem1, $orderItem2, $orderItem3], ['percentage' => 0.2, 'filters' => ['taxons' => ['testTaxon']]])
            ->willReturn([$orderItem1, $orderItem2])
        ;
        $taxonFilter
            ->filter([$orderItem1, $orderItem2], ['percentage' => 0.2, 'filters' => ['taxons' => ['testTaxon']]])
            ->willReturn([$orderItem1])
        ;

        $orderItem1->getQuantity()->willReturn(2);
        $orderItem1->getUnits()->willReturn($units);
        $units->getIterator()->willReturn(new \ArrayIterator([$unit1->getWrappedObject(), $unit2->getWrappedObject()]));

        $orderItem1->getTotal()->willReturn(5);
        $distributor->distribute(1, 2)->willReturn([1, 0]);

        $promotion->getName()->willReturn('Test promotion');

        $adjustmentFactory->createNew()->willReturn($promotionAdjustment1);

        $promotionAdjustment1->setType(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $promotionAdjustment1->setLabel('Test promotion')->shouldBeCalled();
        $promotionAdjustment1->setAmount(-1)->shouldBeCalled();

        $originator->setOrigin($promotionAdjustment1, $promotion)->shouldBeCalled();

        $unit1->addAdjustment($promotionAdjustment1)->shouldBeCalled();
        $unit2->addAdjustment(Argument::any())->shouldNotBeCalled();

        $this->execute($order, ['percentage' => 0.2, 'filters' => ['taxons' => ['testTaxon']]], $promotion);
    }

    function it_throws_exception_if_passed_subject_is_not_order(
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion
    ) {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('execute', [$subject, ['percentage' => 0.2], $promotion])
        ;
    }

    function it_reverts_proper_promotion_adjustment_from_all_units(
        $originator,
        AdjustmentInterface $promotionAdjustment1,
        AdjustmentInterface $promotionAdjustment2,
        Collection $items,
        Collection $units,
        Collection $adjustments,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $unit,
        PromotionInterface $promotion,
        PromotionInterface $someOtherPromotion
    ) {
        $order->getItems()->willReturn($items);
        $items->getIterator()->willReturn(new \ArrayIterator([$orderItem->getWrappedObject()]));

        $orderItem->getUnits()->willReturn($units);
        $units->getIterator()->willReturn(new \ArrayIterator([$unit->getWrappedObject()]));

        $unit->getAdjustments(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)->willReturn($adjustments);
        $adjustments
            ->getIterator()
            ->willReturn(new \ArrayIterator([$promotionAdjustment1->getWrappedObject(), $promotionAdjustment2->getWrappedObject()]))
        ;

        $originator->getOrigin($promotionAdjustment1)->willReturn($promotion);
        $unit->removeAdjustment($promotionAdjustment1)->shouldBeCalled();

        $originator->getOrigin($promotionAdjustment2)->willReturn($someOtherPromotion);
        $unit->removeAdjustment($promotionAdjustment2)->shouldNotBeCalled();

        $this->revert($order, ['percentage' => 0.2], $promotion);
    }

    function it_throws_exception_if_passed_subject_to_revert_is_not_order(
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion
    ) {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('revert', [$subject, ['percentage' => 0.2], $promotion])
        ;
    }

    function it_has_configuration_form_type()
    {
        $this->getConfigurationFormType()->shouldReturn('sylius_promotion_action_percentage_discount_configuration');
    }
}
