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
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Promotion\Action\ItemDiscountAction;
use Sylius\Component\Core\Promotion\Filter\FilterInterface;
use Sylius\Component\Originator\Originator\OriginatorInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ItemFixedDiscountActionSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $adjustmentFactory,
        OriginatorInterface $originator,
        FilterInterface $priceRangeFilter,
        FilterInterface $taxonFilter
    ) {
        $this->beConstructedWith($adjustmentFactory, $originator, $priceRangeFilter, $taxonFilter);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Promotion\Action\ItemFixedDiscountAction');
    }

    function it_is_discount_action()
    {
        $this->shouldHaveType(ItemDiscountAction::class);
    }

    function it_applies_fixed_discount_on_every_unit_in_order(
        $adjustmentFactory,
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
            ->filter([$orderItem1, $orderItem2, $orderItem3], ['amount' => 500, 'filters' => ['taxons' => ['testTaxon']]])
            ->willReturn([$orderItem1, $orderItem2])
        ;
        $taxonFilter
            ->filter([$orderItem1, $orderItem2], ['amount' => 500, 'filters' => ['taxons' => ['testTaxon']]])
            ->willReturn([$orderItem1])
        ;

        $orderItem1->getQuantity()->willReturn(2);
        $orderItem1->getUnits()->willReturn($units);
        $units->getIterator()->willReturn(new \ArrayIterator([$unit1->getWrappedObject(), $unit2->getWrappedObject()]));

        $promotion->getName()->willReturn('Test promotion');

        $adjustmentFactory->createNew()->willReturn($promotionAdjustment1, $promotionAdjustment2);

        $unit1->getTotal()->willReturn(1000);
        $promotionAdjustment1->setType(AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $promotionAdjustment1->setLabel('Test promotion')->shouldBeCalled();
        $promotionAdjustment1->setAmount(-500)->shouldBeCalled();

        $originator->setOrigin($promotionAdjustment1, $promotion)->shouldBeCalled();

        $unit2->getTotal()->willReturn(1000);
        $promotionAdjustment2->setType(AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $promotionAdjustment2->setLabel('Test promotion')->shouldBeCalled();
        $promotionAdjustment2->setAmount(-500)->shouldBeCalled();

        $originator->setOrigin($promotionAdjustment2, $promotion)->shouldBeCalled();

        $unit1->addAdjustment($promotionAdjustment1)->shouldBeCalled();
        $unit2->addAdjustment($promotionAdjustment2)->shouldBeCalled();

        $this->execute($order, ['amount' => 500, 'filters' => ['taxons' => ['testTaxon']]], $promotion);
    }

    function it_does_not_apply_promotions_with_amount_0(
        $adjustmentFactory,
        OrderInterface $order,
        OrderItemUnitInterface $unit1,
        OrderItemUnitInterface $unit2,
        PromotionInterface $promotion
    ) {
        $adjustmentFactory->createNew()->shouldNotBeCalled();

        $unit1->addAdjustment(Argument::any())->shouldNotBeCalled();
        $unit2->addAdjustment(Argument::any())->shouldNotBeCalled();

        $this->execute($order, ['amount' => 0, 'filters' => ['taxons' => ['testTaxon']]], $promotion);
    }

    function it_does_not_apply_bigger_promotions_than_unit_total(
        $adjustmentFactory,
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
            ->filter([$orderItem1, $orderItem2, $orderItem3], ['amount' => 1000, 'filters' => ['taxons' => ['testTaxon']]])
            ->willReturn([$orderItem1, $orderItem3])
        ;
        $taxonFilter
            ->filter([$orderItem1, $orderItem3], ['amount' => 1000, 'filters' => ['taxons' => ['testTaxon']]])
            ->willReturn([$orderItem1])
        ;

        $orderItem1->getQuantity()->willReturn(2);
        $orderItem1->getUnits()->willReturn($units);
        $units->getIterator()->willReturn(new \ArrayIterator([$unit1->getWrappedObject(), $unit2->getWrappedObject()]));

        $promotion->getName()->willReturn('Test promotion');

        $adjustmentFactory->createNew()->willReturn($promotionAdjustment1, $promotionAdjustment2);

        $unit1->getTotal()->willReturn(300);
        $promotionAdjustment1->setType(AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $promotionAdjustment1->setLabel('Test promotion')->shouldBeCalled();
        $promotionAdjustment1->setAmount(-300)->shouldBeCalled();

        $originator->setOrigin($promotionAdjustment1, $promotion)->shouldBeCalled();

        $unit2->getTotal()->willReturn(200);
        $promotionAdjustment2->setType(AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $promotionAdjustment2->setLabel('Test promotion')->shouldBeCalled();
        $promotionAdjustment2->setAmount(-200)->shouldBeCalled();

        $originator->setOrigin($promotionAdjustment2, $promotion)->shouldBeCalled();

        $unit1->addAdjustment($promotionAdjustment1)->shouldBeCalled();
        $unit2->addAdjustment($promotionAdjustment2)->shouldBeCalled();

        $this->execute($order, ['amount' => 1000, 'filters' => ['taxons' => ['testTaxon']]], $promotion);
    }

    function it_throws_exception_if_passed_subject_to_execute_is_not_order(
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion
    ) {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('execute', [$subject, ['amount' => 1000], $promotion])
        ;
    }

    function it_revert_proper_promotion_adjustment_from_all_units(
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

        $unit->getAdjustments(AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT)->willReturn($adjustments);
        $adjustments
            ->getIterator()
            ->willReturn(new \ArrayIterator([$promotionAdjustment1->getWrappedObject(), $promotionAdjustment2->getWrappedObject()]))
        ;

        $originator->getOrigin($promotionAdjustment1)->willReturn($promotion);
        $unit->removeAdjustment($promotionAdjustment1)->shouldBeCalled();

        $originator->getOrigin($promotionAdjustment2)->willReturn($someOtherPromotion);
        $unit->removeAdjustment($promotionAdjustment2)->shouldNotBeCalled();

        $this->revert($order, ['amount' => 1000], $promotion);
    }

    function it_throws_exception_if_passed_subject_to_revert_is_not_order(
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion
    ) {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('revert', [$subject, ['amount' => 1000], $promotion])
        ;
    }

    function it_has_configuration_form_type()
    {
        $this->getConfigurationFormType()->shouldReturn('sylius_promotion_action_fixed_discount_configuration');
    }
}
