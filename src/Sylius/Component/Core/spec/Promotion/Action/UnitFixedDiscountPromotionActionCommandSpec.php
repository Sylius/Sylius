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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Promotion\Action\UnitDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\UnitFixedDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Filter\FilterInterface;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class UnitFixedDiscountPromotionActionCommandSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $adjustmentFactory,
        FilterInterface $priceRangeFilter,
        FilterInterface $taxonFilter,
        FilterInterface $productFilter,
        CurrencyConverterInterface $currencyConverter
    ) {
        $this->beConstructedWith(
            $adjustmentFactory,
            $priceRangeFilter,
            $taxonFilter,
            $productFilter,
            $currencyConverter
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(UnitFixedDiscountPromotionActionCommand::class);
    }

    function it_is_a_discount_action()
    {
        $this->shouldHaveType(UnitDiscountPromotionActionCommand::class);
    }

    function it_applies_a_fixed_discount_on_every_unit_in_order(
        FactoryInterface $adjustmentFactory,
        FilterInterface $priceRangeFilter,
        FilterInterface $taxonFilter,
        FilterInterface $productFilter,
        AdjustmentInterface $promotionAdjustment1,
        AdjustmentInterface $promotionAdjustment2,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $unit1,
        OrderItemUnitInterface $unit2,
        PromotionInterface $promotion
    ) {
        $order->getCurrencyCode()->willReturn('USD');

        $order->getItems()->willReturn(new ArrayCollection([$orderItem]));

        $priceRangeFilter->filter([$orderItem], ['base_amount' => 500])->willReturn([$orderItem]);
        $taxonFilter->filter([$orderItem], ['base_amount' => 500])->willReturn([$orderItem]);
        $productFilter->filter([$orderItem], ['base_amount' => 500])->willReturn([$orderItem]);

        $orderItem->getQuantity()->willReturn(2);
        $orderItem->getUnits()->willReturn(new ArrayCollection([$unit1->getWrappedObject(), $unit2->getWrappedObject()]));

        $promotion->getName()->willReturn('Test promotion');
        $promotion->getCode()->willReturn('TEST_PROMOTION');

        $adjustmentFactory->createNew()->willReturn($promotionAdjustment1, $promotionAdjustment2);

        $unit1->getTotal()->willReturn(1000);
        $promotionAdjustment1->setType(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $promotionAdjustment1->setLabel('Test promotion')->shouldBeCalled();
        $promotionAdjustment1->setAmount(-500)->shouldBeCalled();

        $promotionAdjustment1->setOriginCode('TEST_PROMOTION')->shouldBeCalled();

        $unit2->getTotal()->willReturn(1000);
        $promotionAdjustment2->setType(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $promotionAdjustment2->setLabel('Test promotion')->shouldBeCalled();
        $promotionAdjustment2->setAmount(-500)->shouldBeCalled();

        $promotionAdjustment2->setOriginCode('TEST_PROMOTION')->shouldBeCalled();

        $unit1->addAdjustment($promotionAdjustment1)->shouldBeCalled();
        $unit2->addAdjustment($promotionAdjustment2)->shouldBeCalled();

        $this->execute($order, ['base_amount' => 500], $promotion);
    }

    function it_applies_a_fixed_discount_in_defined_currency_on_every_unit_in_order(
        FactoryInterface $adjustmentFactory,
        FilterInterface $priceRangeFilter,
        FilterInterface $taxonFilter,
        FilterInterface $productFilter,
        CurrencyConverterInterface $currencyConverter,
        AdjustmentInterface $promotionAdjustment1,
        AdjustmentInterface $promotionAdjustment2,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $unit1,
        OrderItemUnitInterface $unit2,
        PromotionInterface $promotion
    ) {
        $order->getCurrencyCode()->willReturn('PLN');

        $currencyConverter->convertToBase(1000, 'PLN')->willReturn(250);

        $order->getItems()->willReturn(new ArrayCollection([$orderItem]));

        $priceRangeFilter->filter([$orderItem], ['base_amount' => 500, 'amounts' => ['PLN' => 1000]])->willReturn([$orderItem]);
        $taxonFilter->filter([$orderItem], ['base_amount' => 500, 'amounts' => ['PLN' => 1000]])->willReturn([$orderItem]);
        $productFilter->filter([$orderItem], ['base_amount' => 500, 'amounts' => ['PLN' => 1000]])->willReturn([$orderItem]);

        $orderItem->getQuantity()->willReturn(2);
        $orderItem->getUnits()->willReturn(new ArrayCollection([$unit1->getWrappedObject(), $unit2->getWrappedObject()]));

        $promotion->getName()->willReturn('Test promotion');
        $promotion->getCode()->willReturn('TEST_PROMOTION');

        $adjustmentFactory->createNew()->willReturn($promotionAdjustment1, $promotionAdjustment2);

        $unit1->getTotal()->willReturn(1000);
        $promotionAdjustment1->setType(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $promotionAdjustment1->setLabel('Test promotion')->shouldBeCalled();
        $promotionAdjustment1->setAmount(-250)->shouldBeCalled();

        $promotionAdjustment1->setOriginCode('TEST_PROMOTION')->shouldBeCalled();

        $unit2->getTotal()->willReturn(1000);
        $promotionAdjustment2->setType(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $promotionAdjustment2->setLabel('Test promotion')->shouldBeCalled();
        $promotionAdjustment2->setAmount(-250)->shouldBeCalled();

        $promotionAdjustment2->setOriginCode('TEST_PROMOTION')->shouldBeCalled();

        $unit1->addAdjustment($promotionAdjustment1)->shouldBeCalled();
        $unit2->addAdjustment($promotionAdjustment2)->shouldBeCalled();

        $this->execute($order, ['base_amount' => 500, 'amounts' => ['PLN' => 1000]], $promotion);
    }

    function it_does_not_apply_promotions_with_amount_0(
        FactoryInterface $adjustmentFactory,
        OrderInterface $order,
        OrderItemUnitInterface $unit1,
        OrderItemUnitInterface $unit2,
        PromotionInterface $promotion
    ) {
        $adjustmentFactory->createNew()->shouldNotBeCalled();

        $unit1->addAdjustment(Argument::any())->shouldNotBeCalled();
        $unit2->addAdjustment(Argument::any())->shouldNotBeCalled();

        $this->execute($order, ['base_amount' => 0], $promotion);
    }

    function it_does_not_apply_bigger_promotions_than_unit_total(
        FactoryInterface $adjustmentFactory,
        FilterInterface $priceRangeFilter,
        FilterInterface $taxonFilter,
        FilterInterface $productFilter,
        AdjustmentInterface $promotionAdjustment1,
        AdjustmentInterface $promotionAdjustment2,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $unit1,
        OrderItemUnitInterface $unit2,
        PromotionInterface $promotion
    ) {
        $order->getCurrencyCode()->willReturn('USD');

        $order->getItems()->willReturn(new ArrayCollection([$orderItem]));

        $priceRangeFilter->filter([$orderItem], ['base_amount' => 1000])->willReturn([$orderItem]);
        $taxonFilter->filter([$orderItem], ['base_amount' => 1000])->willReturn([$orderItem]);
        $productFilter->filter([$orderItem], ['base_amount' => 1000])->willReturn([$orderItem]);

        $orderItem->getQuantity()->willReturn(2);
        $orderItem->getUnits()->willReturn(new ArrayCollection([$unit1->getWrappedObject(), $unit2->getWrappedObject()]));

        $promotion->getName()->willReturn('Test promotion');
        $promotion->getCode()->willReturn('TEST_PROMOTION');

        $adjustmentFactory->createNew()->willReturn($promotionAdjustment1, $promotionAdjustment2);

        $unit1->getTotal()->willReturn(300);
        $promotionAdjustment1->setType(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $promotionAdjustment1->setLabel('Test promotion')->shouldBeCalled();
        $promotionAdjustment1->setAmount(-300)->shouldBeCalled();

        $promotionAdjustment1->setOriginCode('TEST_PROMOTION')->shouldBeCalled();

        $unit2->getTotal()->willReturn(200);
        $promotionAdjustment2->setType(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $promotionAdjustment2->setLabel('Test promotion')->shouldBeCalled();
        $promotionAdjustment2->setAmount(-200)->shouldBeCalled();

        $promotionAdjustment2->setOriginCode('TEST_PROMOTION')->shouldBeCalled();

        $unit1->addAdjustment($promotionAdjustment1)->shouldBeCalled();
        $unit2->addAdjustment($promotionAdjustment2)->shouldBeCalled();

        $this->execute($order, ['base_amount' => 1000], $promotion);
    }

    function it_throws_an_exception_if_passed_subject_to_execute_is_not_order(
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion
    ) {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('execute', [$subject, ['base_amount' => 1000], $promotion])
        ;
    }

    function it_reverts_a_proper_promotion_adjustment_from_all_units(
        AdjustmentInterface $promotionAdjustment1,
        AdjustmentInterface $promotionAdjustment2,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $unit,
        PromotionInterface $promotion
    ) {
        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));

        $orderItem->getUnits()->willReturn(new ArrayCollection([$unit->getWrappedObject()]));

        $unit->getAdjustments(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)->willReturn(
            new ArrayCollection([
                $promotionAdjustment1->getWrappedObject(),
                $promotionAdjustment2->getWrappedObject(),
            ]))
        ;

        $promotion->getCode()->willReturn('PROMOTION');

        $promotionAdjustment1->getOriginCode()->willReturn('PROMOTION');
        $unit->removeAdjustment($promotionAdjustment1)->shouldBeCalled();

        $promotionAdjustment2->getOriginCode()->willReturn('OTHER_PROMOTION');
        $unit->removeAdjustment($promotionAdjustment2)->shouldNotBeCalled();

        $this->revert($order, ['base_amount' => 1000], $promotion);
    }

    function it_throws_an_exception_if_passed_subject_to_revert_is_not_order(
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion
    ) {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('revert', [$subject, ['base_amount' => 1000], $promotion])
        ;
    }

    function it_has_a_configuration_form_type()
    {
        $this->getConfigurationFormType()->shouldReturn('sylius_promotion_action_unit_fixed_discount_configuration');
    }
}
