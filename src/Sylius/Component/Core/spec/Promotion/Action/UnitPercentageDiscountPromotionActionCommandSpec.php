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

namespace spec\Sylius\Component\Core\Promotion\Action;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Promotion\Action\UnitDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Filter\FilterInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class UnitPercentageDiscountPromotionActionCommandSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $adjustmentFactory,
        FilterInterface $priceRangeFilter,
        FilterInterface $taxonFilter,
        FilterInterface $productFilter
    ): void {
        $this->beConstructedWith($adjustmentFactory, $priceRangeFilter, $taxonFilter, $productFilter);
    }

    function it_is_an_item_discount_action(): void
    {
        $this->shouldHaveType(UnitDiscountPromotionActionCommand::class);
    }

    function it_applies_percentage_discount_on_every_unit_in_order(
        ChannelInterface $channel,
        FactoryInterface $adjustmentFactory,
        FilterInterface $priceRangeFilter,
        FilterInterface $taxonFilter,
        FilterInterface $productFilter,
        AdjustmentInterface $promotionAdjustment1,
        AdjustmentInterface $promotionAdjustment2,
        Collection $originalItems,
        Collection $units,
        OrderInterface $order,
        OrderItemInterface $orderItem1,
        OrderItemUnitInterface $unit1,
        OrderItemUnitInterface $unit2,
        PromotionInterface $promotion
    ): void {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');

        $order->getItems()->willReturn($originalItems);
        $originalItems->toArray()->willReturn([$orderItem1]);

        $priceRangeFilter->filter([$orderItem1], ['percentage' => 0.2, 'channel' => $channel])->willReturn([$orderItem1]);
        $taxonFilter->filter([$orderItem1], ['percentage' => 0.2])->willReturn([$orderItem1]);
        $productFilter->filter([$orderItem1], ['percentage' => 0.2])->willReturn([$orderItem1]);

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

        $this->execute($order, ['WEB_US' => ['percentage' => 0.2]], $promotion)->shouldReturn(true);
    }

    function it_does_not_apply_a_discount_if_all_items_have_been_filtered_out(
        ChannelInterface $channel,
        FilterInterface $priceRangeFilter,
        FilterInterface $taxonFilter,
        FilterInterface $productFilter,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        PromotionInterface $promotion
    ): void {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');

        $order->getItems()->willReturn(new ArrayCollection([$orderItem]));
        $order->getChannel()->willReturn($channel);

        $priceRangeFilter->filter([$orderItem], ['percentage' => 0.2, 'channel' => $channel])->willReturn([$orderItem]);
        $taxonFilter->filter([$orderItem], ['percentage' => 0.2])->willReturn([$orderItem]);
        $productFilter->filter([$orderItem], ['percentage' => 0.2])->willReturn([]);

        $this->execute($order, ['WEB_US' => ['percentage' => 0.2]], $promotion)->shouldReturn(false);
    }

    function it_does_not_apply_discount_if_configuration_for_order_channel_is_not_defined(
        ChannelInterface $channel,
        OrderInterface $order,
        PromotionInterface $promotion
    ): void {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_PL');

        $order->getItems()->shouldNotBeCalled();

        $this->execute($order, ['WEB_US' => ['percentage' => 0.2]], $promotion)->shouldReturn(false);
    }

    function it_does_not_apply_discount_if_percentage_configuration_not_defined(
        ChannelInterface $channel,
        OrderInterface $order,
        PromotionInterface $promotion
    ): void {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_PL');

        $order->getItems()->shouldNotBeCalled();

        $this->execute($order, ['WEB_PL' => []], $promotion)->shouldReturn(false);
    }

    function it_throws_an_exception_if_passed_subject_is_not_order(
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion
    ): void {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('execute', [$subject, ['percentage' => 0.2], $promotion])
        ;
    }

    function it_reverts_a_proper_promotion_adjustment_from_all_units(
        AdjustmentInterface $promotionAdjustment1,
        AdjustmentInterface $promotionAdjustment2,
        Collection $items,
        Collection $units,
        Collection $adjustments,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $unit,
        PromotionInterface $promotion
    ): void {
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

    function it_throws_an_exception_if_passed_subject_to_revert_is_not_order(
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion
    ): void {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('revert', [$subject, ['percentage' => 0.2], $promotion])
        ;
    }
}
