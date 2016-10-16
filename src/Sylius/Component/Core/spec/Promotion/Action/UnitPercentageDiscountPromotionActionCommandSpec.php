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
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Promotion\Action\UnitDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\UnitPercentageDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Filter\FilterInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class UnitPercentageDiscountPromotionActionCommandSpec extends ObjectBehavior
{
    public function let(
        FactoryInterface $adjustmentFactory,
        FilterInterface $priceRangeFilter,
        FilterInterface $taxonFilter
    ) {
        $this->beConstructedWith($adjustmentFactory, $priceRangeFilter, $taxonFilter);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(UnitPercentageDiscountPromotionActionCommand::class);
    }

<<<<<<< 79e1ae723f64dd234dbbb0a1b0829f58988422ab
    function it_is_an_item_discount_action()
=======
    public function it_is_item_discount_action()
>>>>>>> added taxons filters
    {
        $this->shouldHaveType(UnitDiscountPromotionActionCommand::class);
    }

<<<<<<< 79e1ae723f64dd234dbbb0a1b0829f58988422ab
    function it_applies_percentage_discount_on_every_unit_in_order(
        FactoryInterface $adjustmentFactory,
        FilterInterface $priceRangeFilter,
        FilterInterface $taxonFilter,
=======
    public function it_applies_percentage_discount_on_every_unit_in_order(
        $adjustmentFactory,
        $priceRangeFilter,
        $taxonFilter,
>>>>>>> added taxons filters
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

        $orderItem1->getUnitPrice()->willReturn(500);

        $promotion->getName()->willReturn('Test promotion');
        $promotion->getCode()->willReturn('TEST_PROMOTION');

        $adjustmentFactory->createNew()->willReturn($promotionAdjustment1, $promotionAdjustment2);

        $promotionAdjustment1->setType(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $promotionAdjustment1->setLabel('Test promotion')->shouldBeCalled();
        $promotionAdjustment1->setAmount(-100)->shouldBeCalled();

        $promotionAdjustment1->setOriginCode('TEST_PROMOTION')->shouldBeCalled();

        $promotionAdjustment2->setType(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $promotionAdjustment2->setLabel('Test promotion')->shouldBeCalled();
        $promotionAdjustment2->setAmount(-100)->shouldBeCalled();

        $promotionAdjustment2->setOriginCode('TEST_PROMOTION')->shouldBeCalled();

        $unit1->addAdjustment($promotionAdjustment1)->shouldBeCalled();
        $unit2->addAdjustment($promotionAdjustment2)->shouldBeCalled();

        $this->execute($order, ['percentage' => 0.2, 'filters' => ['taxons' => ['testTaxon']]], $promotion);
    }

<<<<<<< 79e1ae723f64dd234dbbb0a1b0829f58988422ab
    function it_throws_an_exception_if_passed_subject_is_not_order(
=======
    public function it_throws_exception_if_passed_subject_is_not_order(
>>>>>>> added taxons filters
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion
    ) {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('execute', [$subject, ['percentage' => 0.2], $promotion])
        ;
    }

<<<<<<< 79e1ae723f64dd234dbbb0a1b0829f58988422ab
    function it_reverts_a_proper_promotion_adjustment_from_all_units(
=======
    public function it_reverts_proper_promotion_adjustment_from_all_units(
>>>>>>> added taxons filters
        AdjustmentInterface $promotionAdjustment1,
        AdjustmentInterface $promotionAdjustment2,
        Collection $items,
        Collection $units,
        Collection $adjustments,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $unit,
        PromotionInterface $promotion
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

        $promotion->getCode()->willReturn('PROMOTION');

        $promotionAdjustment1->getOriginCode()->willReturn('PROMOTION');
        $unit->removeAdjustment($promotionAdjustment1)->shouldBeCalled();

        $promotionAdjustment2->getOriginCode()->willReturn('OTHER_PROMOTION');
        $unit->removeAdjustment($promotionAdjustment2)->shouldNotBeCalled();

        $this->revert($order, ['percentage' => 0.2], $promotion);
    }

<<<<<<< 79e1ae723f64dd234dbbb0a1b0829f58988422ab
    function it_throws_an_exception_if_passed_subject_to_revert_is_not_order(
=======
    public function it_throws_exception_if_passed_subject_to_revert_is_not_order(
>>>>>>> added taxons filters
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion
    ) {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('revert', [$subject, ['percentage' => 0.2], $promotion])
        ;
    }

<<<<<<< 79e1ae723f64dd234dbbb0a1b0829f58988422ab
    function it_has_a_configuration_form_type()
=======
    public function it_has_configuration_form_type()
>>>>>>> added taxons filters
    {
        $this->getConfigurationFormType()->shouldReturn('sylius_promotion_action_unit_percentage_discount_configuration');
    }
}
