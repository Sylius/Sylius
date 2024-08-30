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

namespace spec\Sylius\Component\Core\Promotion\Action;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Promotion\Action\UnitDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Filter\FilterInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Resource\Factory\FactoryInterface;

final class UnitFixedDiscountPromotionActionCommandSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $adjustmentFactory,
        FilterInterface $priceRangeFilter,
        FilterInterface $taxonFilter,
        FilterInterface $productFilter,
    ): void {
        $this->beConstructedWith(
            $adjustmentFactory,
            $priceRangeFilter,
            $taxonFilter,
            $productFilter,
        );
    }

    function it_is_a_discount_action(): void
    {
        $this->shouldHaveType(UnitDiscountPromotionActionCommand::class);
    }

    function it_applies_a_fixed_discount_on_every_unit_in_order(
        FactoryInterface $adjustmentFactory,
        FilterInterface $priceRangeFilter,
        FilterInterface $taxonFilter,
        FilterInterface $productFilter,
        ChannelInterface $channel,
        AdjustmentInterface $promotionAdjustment1,
        AdjustmentInterface $promotionAdjustment2,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $unit1,
        OrderItemUnitInterface $unit2,
        OrderItemInterface $orderItem1,
        OrderItemInterface $orderItem2,
        ProductVariantInterface $productVariant1,
        ProductVariantInterface $productVariant2,
        PromotionInterface $promotion,
        ChannelPricingInterface $channelPricing1,
        ChannelPricingInterface $channelPricing2,
    ): void {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');

        $unit1->getOrderItem()->willReturn($orderItem1);
        $unit2->getOrderItem()->willReturn($orderItem2);

        $orderItem1->getVariant()->willReturn($productVariant1);
        $orderItem2->getVariant()->willReturn($productVariant2);
        $orderItem1->getOrder()->willReturn($order);
        $orderItem2->getOrder()->willReturn($order);

        $productVariant1->getChannelPricingForChannel($channel)->willReturn($channelPricing1);
        $productVariant2->getChannelPricingForChannel($channel)->willReturn($channelPricing2);

        $channelPricing1->getMinimumPrice()->willReturn(0);
        $channelPricing2->getMinimumPrice()->willReturn(0);

        $order->getItems()->willReturn(new ArrayCollection([$orderItem]));

        $priceRangeFilter->filter([$orderItem], ['amount' => 500, 'channel' => $channel])->willReturn([$orderItem]);
        $taxonFilter->filter([$orderItem], ['amount' => 500])->willReturn([$orderItem]);
        $productFilter->filter([$orderItem], ['amount' => 500])->willReturn([$orderItem]);

        $orderItem->getQuantity()->willReturn(2);
        $orderItem->getUnits()->willReturn(new ArrayCollection([$unit1->getWrappedObject(), $unit2->getWrappedObject()]));

        $promotion->getName()->willReturn('Test promotion');
        $promotion->getCode()->willReturn('TEST_PROMOTION');
        $promotion->getAppliesToDiscounted()->willReturn(true);

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

        $this->execute($order, ['WEB_US' => ['amount' => 500]], $promotion)->shouldReturn(true);
    }

    function it_applies_a_fixed_discount_only_to_non_discounted_units_if_promotion_does_not_apply_to_discounted_ones(
        FactoryInterface $adjustmentFactory,
        FilterInterface $priceRangeFilter,
        FilterInterface $taxonFilter,
        FilterInterface $productFilter,
        ChannelInterface $channel,
        OrderInterface $order,
        OrderItemInterface $orderItem1,
        OrderItemInterface $orderItem2,
        OrderItemUnitInterface $unit1,
        OrderItemUnitInterface $unit2,
        PromotionInterface $promotion,
        ProductVariantInterface $variant1,
        ProductVariantInterface $variant2,
        AdjustmentInterface $promotionAdjustment,
        ChannelPricingInterface $channelPricing1,
        ChannelPricingInterface $channelPricing2,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');

        $order->getItems()->willReturn(new ArrayCollection([$orderItem1->getWrappedObject(), $orderItem2->getWrappedObject()]));

        $priceRangeFilter->filter([$orderItem1, $orderItem2], ['amount' => 500, 'channel' => $channel])->willReturn([$orderItem1, $orderItem2]);
        $taxonFilter->filter([$orderItem1, $orderItem2], ['amount' => 500])->willReturn([$orderItem1, $orderItem2]);
        $productFilter->filter([$orderItem1, $orderItem2], ['amount' => 500])->willReturn([$orderItem1, $orderItem2]);

        $orderItem1->getQuantity()->willReturn(1);
        $orderItem1->getUnits()->willReturn(new ArrayCollection([$unit1->getWrappedObject()]));
        $orderItem1->getVariant()->willReturn($variant1);
        $orderItem1->getOrder()->willReturn($order);
        $variant1->getAppliedPromotionsForChannel($channel)->willReturn(new ArrayCollection());
        $unit1->getTotal()->willReturn(1000);
        $unit1->getOrderItem()->willReturn($orderItem1);

        $orderItem2->getQuantity()->willReturn(1);
        $orderItem2->getUnits()->willReturn(new ArrayCollection([$unit2->getWrappedObject()]));
        $orderItem2->getVariant()->willReturn($variant2);
        $orderItem2->getOrder()->willReturn($order);
        $variant2->getAppliedPromotionsForChannel($channel)->willReturn(new ArrayCollection([$catalogPromotion->getWrappedObject()]));
        $unit2->getTotal()->willReturn(1000);
        $unit2->getOrderItem()->willReturn($orderItem2);

        $variant1->getChannelPricingForChannel($channel)->willReturn($channelPricing1);
        $variant2->getChannelPricingForChannel($channel)->willReturn($channelPricing2);

        $channelPricing1->getMinimumPrice()->willReturn(0);
        $channelPricing2->getMinimumPrice()->willReturn(0);

        $promotion->getName()->willReturn('Test promotion');
        $promotion->getCode()->willReturn('TEST_PROMOTION');
        $promotion->getAppliesToDiscounted()->willReturn(false);

        $adjustmentFactory->createNew()->willReturn($promotionAdjustment);
        $promotionAdjustment->setType(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $promotionAdjustment->setLabel('Test promotion')->shouldBeCalled();
        $promotionAdjustment->setAmount(-500)->shouldBeCalled();
        $promotionAdjustment->setOriginCode('TEST_PROMOTION')->shouldBeCalled();

        $unit1->addAdjustment($promotionAdjustment)->shouldBeCalled();
        $unit2->addAdjustment(Argument::any())->shouldNotBeCalled();

        $this->execute($order, ['WEB_US' => ['amount' => 500]], $promotion)->shouldReturn(true);
    }

    function it_does_not_apply_a_discount_if_all_items_have_been_filtered_out(
        FilterInterface $priceRangeFilter,
        FilterInterface $taxonFilter,
        FilterInterface $productFilter,
        ChannelInterface $channel,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        PromotionInterface $promotion,
    ): void {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');

        $order->getItems()->willReturn(new ArrayCollection([$orderItem]));

        $priceRangeFilter->filter([$orderItem], ['amount' => 500, 'channel' => $channel])->willReturn([$orderItem]);
        $taxonFilter->filter([$orderItem], ['amount' => 500])->willReturn([$orderItem]);
        $productFilter->filter([$orderItem], ['amount' => 500])->willReturn([]);

        $this->execute($order, ['WEB_US' => ['amount' => 500]], $promotion)->shouldReturn(false);
    }

    function it_does_not_apply_discount_with_amount_0(
        FactoryInterface $adjustmentFactory,
        ChannelInterface $channel,
        OrderInterface $order,
        OrderItemUnitInterface $unit1,
        OrderItemUnitInterface $unit2,
        PromotionInterface $promotion,
    ): void {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');

        $adjustmentFactory->createNew()->shouldNotBeCalled();

        $unit1->addAdjustment(Argument::any())->shouldNotBeCalled();
        $unit2->addAdjustment(Argument::any())->shouldNotBeCalled();

        $this->execute($order, ['WEB_US' => ['amount' => 0]], $promotion)->shouldReturn(false);
    }

    function it_does_not_apply_bigger_discount_than_unit_total(
        FactoryInterface $adjustmentFactory,
        FilterInterface $priceRangeFilter,
        FilterInterface $taxonFilter,
        FilterInterface $productFilter,
        ChannelInterface $channel,
        AdjustmentInterface $promotionAdjustment1,
        AdjustmentInterface $promotionAdjustment2,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $unit1,
        OrderItemUnitInterface $unit2,
        OrderItemInterface $orderItem1,
        OrderItemInterface $orderItem2,
        ProductVariantInterface $productVariant1,
        ProductVariantInterface $productVariant2,
        PromotionInterface $promotion,
        ChannelPricingInterface $channelPricing1,
        ChannelPricingInterface $channelPricing2,
    ): void {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');

        $unit1->getOrderItem()->willReturn($orderItem1);
        $unit2->getOrderItem()->willReturn($orderItem2);

        $orderItem1->getVariant()->willReturn($productVariant1);
        $orderItem2->getVariant()->willReturn($productVariant2);
        $orderItem1->getOrder()->willReturn($order);
        $orderItem2->getOrder()->willReturn($order);

        $productVariant1->getChannelPricingForChannel($channel)->willReturn($channelPricing1);
        $productVariant2->getChannelPricingForChannel($channel)->willReturn($channelPricing2);

        $channelPricing1->getMinimumPrice()->willReturn(0);
        $channelPricing2->getMinimumPrice()->willReturn(0);

        $order->getItems()->willReturn(new ArrayCollection([$orderItem]));

        $priceRangeFilter->filter([$orderItem], ['amount' => 1000, 'channel' => $channel])->willReturn([$orderItem]);
        $taxonFilter->filter([$orderItem], ['amount' => 1000])->willReturn([$orderItem]);
        $productFilter->filter([$orderItem], ['amount' => 1000])->willReturn([$orderItem]);

        $orderItem->getQuantity()->willReturn(2);
        $orderItem->getUnits()->willReturn(new ArrayCollection([$unit1->getWrappedObject(), $unit2->getWrappedObject()]));

        $promotion->getName()->willReturn('Test promotion');
        $promotion->getCode()->willReturn('TEST_PROMOTION');
        $promotion->getAppliesToDiscounted()->willReturn(true);

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

        $this->execute($order, ['WEB_US' => ['amount' => 1000]], $promotion)->shouldReturn(true);
    }

    function it_does_not_apply_bigger_discount_than_variant_minimum_price(
        ChannelInterface $channel,
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
        OrderItemInterface $orderItem1,
        OrderItemInterface $orderItem2,
        ProductVariantInterface $productVariant1,
        ProductVariantInterface $productVariant2,
        PromotionInterface $promotion,
        ChannelPricingInterface $channelPricing1,
        ChannelPricingInterface $channelPricing2,
    ): void {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');

        $unit1->getOrderItem()->willReturn($orderItem1);
        $unit2->getOrderItem()->willReturn($orderItem1);

        $orderItem1->getVariant()->willReturn($productVariant1);

        $orderItem1->getOrder()->willReturn($order);

        $productVariant1->getChannelPricingForChannel($channel)->willReturn($channelPricing1);
        $productVariant2->getChannelPricingForChannel($channel)->willReturn($channelPricing2);

        $channelPricing1->getMinimumPrice()->willReturn(200);
        $channelPricing2->getMinimumPrice()->willReturn(150);
        $promotion->getAppliesToDiscounted()->willReturn(true);

        $order->getItems()->willReturn(new ArrayCollection([$orderItem1, $orderItem2]));

        $priceRangeFilter->filter([$orderItem1, $orderItem2], ['amount' => 1000, 'channel' => $channel])->willReturn([$orderItem1, $orderItem2]);
        $taxonFilter->filter([$orderItem1, $orderItem2], ['amount' => 1000])->willReturn([$orderItem1, $orderItem2]);
        $productFilter->filter([$orderItem1, $orderItem2], ['amount' => 1000])->willReturn([$orderItem1, $orderItem2]);

        $orderItem1->getQuantity()->willReturn(2);
        $orderItem1->getUnits()->willReturn(new ArrayCollection([$unit1->getWrappedObject(), $unit2->getWrappedObject()]));

        $orderItem2->getQuantity()->willReturn(1);
        $orderItem2->getUnits()->willReturn(new ArrayCollection([$unit2->getWrappedObject()]));

        $promotion->getName()->willReturn('Test promotion');
        $promotion->getCode()->willReturn('TEST_PROMOTION');

        $adjustmentFactory->createNew()->willReturn($promotionAdjustment1, $promotionAdjustment2);

        $unit1->getTotal()->willReturn(300);
        $promotionAdjustment1->setType(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $promotionAdjustment1->setLabel('Test promotion')->shouldBeCalled();
        $promotionAdjustment1->setAmount(-100)->shouldBeCalled();

        $promotionAdjustment1->setOriginCode('TEST_PROMOTION')->shouldBeCalled();

        $unit2->getTotal()->willReturn(300);
        $promotionAdjustment2->setType(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $promotionAdjustment2->setLabel('Test promotion')->shouldBeCalled();
        $promotionAdjustment2->setAmount(-100)->shouldBeCalled();

        $promotionAdjustment2->setOriginCode('TEST_PROMOTION')->shouldBeCalled();

        $unit1->addAdjustment($promotionAdjustment1)->shouldBeCalled();
        $unit2->addAdjustment($promotionAdjustment2)->shouldBeCalled();

        $this->execute($order, ['WEB_US' => ['amount' => 1000]], $promotion)->shouldReturn(true);
    }

    function it_does_not_apply_discount_if_no_amount_is_defined_for_order_channel(
        FactoryInterface $adjustmentFactory,
        ChannelInterface $channel,
        OrderInterface $order,
        PromotionInterface $promotion,
    ): void {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');

        $adjustmentFactory->createNew()->shouldNotBeCalled();

        $this->execute($order, ['WEB_PL' => ['amount' => 0]], $promotion)->shouldReturn(false);
    }

    function it_throws_an_exception_if_passed_subject_to_execute_is_not_order(
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion,
    ): void {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('execute', [$subject, ['amount' => 1000], $promotion])
        ;
    }

    function it_reverts_a_proper_promotion_adjustment_from_all_units(
        AdjustmentInterface $promotionAdjustment1,
        AdjustmentInterface $promotionAdjustment2,
        ChannelInterface $channel,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $unit,
        PromotionInterface $promotion,
    ): void {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');

        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));

        $orderItem->getUnits()->willReturn(new ArrayCollection([$unit->getWrappedObject()]));

        $unit->getAdjustments(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)->willReturn(
            new ArrayCollection([
                $promotionAdjustment1->getWrappedObject(),
                $promotionAdjustment2->getWrappedObject(),
            ]),
        )
        ;

        $promotion->getCode()->willReturn('PROMOTION');

        $promotionAdjustment1->getOriginCode()->willReturn('PROMOTION');
        $unit->removeAdjustment($promotionAdjustment1)->shouldBeCalled();

        $promotionAdjustment2->getOriginCode()->willReturn('OTHER_PROMOTION');
        $unit->removeAdjustment($promotionAdjustment2)->shouldNotBeCalled();

        $this->revert($order, ['WEB_US' => ['amount' => 1000]], $promotion);
    }

    function it_throws_an_exception_if_passed_subject_to_revert_is_not_order(
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion,
    ): void {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('revert', [$subject, ['amount' => 1000], $promotion])
        ;
    }
}
