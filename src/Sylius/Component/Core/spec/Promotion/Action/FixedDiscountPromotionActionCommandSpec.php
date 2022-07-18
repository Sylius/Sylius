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
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Distributor\MinimumPriceDistributorInterface;
use Sylius\Component\Core\Distributor\ProportionalIntegerDistributorInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Promotion\Applicator\UnitsPromotionAdjustmentsApplicatorInterface;
use Sylius\Component\Promotion\Action\PromotionActionCommandInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

final class FixedDiscountPromotionActionCommandSpec extends ObjectBehavior
{
    function let(
        ProportionalIntegerDistributorInterface $distributor,
        UnitsPromotionAdjustmentsApplicatorInterface $unitsPromotionAdjustmentsApplicator,
        MinimumPriceDistributorInterface $minimumPriceDistributor,
    ): void {
        $this->beConstructedWith(
            $distributor,
            $unitsPromotionAdjustmentsApplicator,
            $minimumPriceDistributor,
        );
    }

    function it_implements_promotion_action_interface(): void
    {
        $this->shouldImplement(PromotionActionCommandInterface::class);
    }

    function it_uses_a_distributor_and_applicator_to_execute_promotion_action(
        ChannelInterface $channel,
        OrderInterface $order,
        OrderItemInterface $firstItem,
        OrderItemInterface $secondItem,
        PromotionInterface $promotion,
        MinimumPriceDistributorInterface $minimumPriceDistributor,
        UnitsPromotionAdjustmentsApplicatorInterface $unitsPromotionAdjustmentsApplicator,
        ProductVariantInterface $productVariantOne,
        ProductVariantInterface $productVariantTwo,
        ChannelPricingInterface $channelPricingOne,
        ChannelPricingInterface $channelPricingTwo,
    ): void {
        $channel->getCode()->willReturn('WEB_US');

        $order->getCurrencyCode()->willReturn('USD');
        $order->getChannel()->willReturn($channel);
        $order->countItems()->willReturn(2);
        $order
            ->getItems()
            ->willReturn(new ArrayCollection([$firstItem->getWrappedObject(), $secondItem->getWrappedObject()]))
        ;

        $firstItem->getVariant()->willReturn($productVariantOne);
        $secondItem->getVariant()->willReturn($productVariantTwo);
        $productVariantOne->getChannelPricingForChannel($channel)->willReturn($channelPricingOne);
        $productVariantTwo->getChannelPricingForChannel($channel)->willReturn($channelPricingTwo);

        $channelPricingOne->getMinimumPrice()->willReturn(0);
        $channelPricingTwo->getMinimumPrice()->willReturn(0);

        $order->getPromotionSubjectTotal()->willReturn(10000);

        $promotion->getAppliesToDiscounted()->willReturn(true);

        $firstItem->getTotal()->willReturn(6000);
        $firstItem->getQuantity()->willReturn(1);
        $secondItem->getTotal()->willReturn(4000);
        $secondItem->getQuantity()->willReturn(1);

        $minimumPriceDistributor->distribute([$firstItem, $secondItem], -1000, $channel, true)->willReturn([-600, -400]);
        $unitsPromotionAdjustmentsApplicator->apply($order, $promotion, [-600, -400])->shouldBeCalled();

        $this->execute($order, ['WEB_US' => ['amount' => 1000]], $promotion)->shouldReturn(true);
    }

    function it_distributes_promotion_using_regular_distributor_if_minimum_price_distributor_is_not_provided(
        ChannelInterface $channel,
        OrderInterface $order,
        OrderItemInterface $firstItem,
        OrderItemInterface $secondItem,
        PromotionInterface $promotion,
        MinimumPriceDistributorInterface $minimumPriceDistributor,
        ProportionalIntegerDistributorInterface $distributor,
        UnitsPromotionAdjustmentsApplicatorInterface $unitsPromotionAdjustmentsApplicator,
        ProductVariantInterface $productVariantOne,
        ProductVariantInterface $productVariantTwo,
        ChannelPricingInterface $channelPricingOne,
        ChannelPricingInterface $channelPricingTwo,
    ): void {
        $this->beConstructedWith(
            $distributor,
            $unitsPromotionAdjustmentsApplicator,
        );

        $order->getCurrencyCode()->willReturn('USD');
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');

        $order->countItems()->willReturn(2);

        $order
            ->getItems()
            ->willReturn(new ArrayCollection([$firstItem->getWrappedObject(), $secondItem->getWrappedObject()]))
        ;

        $promotion->getAppliesToDiscounted()->willReturn(true);

        $firstItem->getVariant()->willReturn($productVariantOne);
        $secondItem->getVariant()->willReturn($productVariantTwo);
        $productVariantOne->getChannelPricingForChannel($channel)->willReturn($channelPricingOne);
        $productVariantTwo->getChannelPricingForChannel($channel)->willReturn($channelPricingTwo);

        $channelPricingOne->getMinimumPrice()->willReturn(5800);
        $channelPricingTwo->getMinimumPrice()->willReturn(0);

        $order->getPromotionSubjectTotal()->willReturn(10000);
        $firstItem->getTotal()->willReturn(6000);
        $firstItem->getQuantity()->willReturn(1);
        $secondItem->getTotal()->willReturn(4000);
        $secondItem->getQuantity()->willReturn(1);

        $minimumPriceDistributor->distribute([$firstItem, $secondItem], -1000, $channel, true)->shouldNotBeCalled();
        $distributor->distribute([6000, 4000], -1000)->willReturn([-200, -800]);

        $unitsPromotionAdjustmentsApplicator->apply($order, $promotion, [-200, -800])->shouldBeCalled();

        $this->execute($order, ['WEB_US' => ['amount' => 1000]], $promotion)->shouldReturn(true);
    }

    function it_distributes_promotion_up_to_minimum_price_of_variant(
        ChannelInterface $channel,
        OrderInterface $order,
        OrderItemInterface $firstItem,
        OrderItemInterface $secondItem,
        PromotionInterface $promotion,
        MinimumPriceDistributorInterface $minimumPriceDistributor,
        UnitsPromotionAdjustmentsApplicatorInterface $unitsPromotionAdjustmentsApplicator,
        ProductVariantInterface $productVariantOne,
        ProductVariantInterface $productVariantTwo,
        ChannelPricingInterface $channelPricingOne,
        ChannelPricingInterface $channelPricingTwo,
    ): void {
        $order->getCurrencyCode()->willReturn('USD');
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');

        $order->countItems()->willReturn(2);

        $order
            ->getItems()
            ->willReturn(new ArrayCollection([$firstItem->getWrappedObject(), $secondItem->getWrappedObject()]))
        ;

        $firstItem->getVariant()->willReturn($productVariantOne);
        $secondItem->getVariant()->willReturn($productVariantTwo);
        $productVariantOne->getChannelPricingForChannel($channel)->willReturn($channelPricingOne);
        $productVariantTwo->getChannelPricingForChannel($channel)->willReturn($channelPricingTwo);

        $channelPricingOne->getMinimumPrice()->willReturn(5800);
        $channelPricingTwo->getMinimumPrice()->willReturn(0);

        $order->getPromotionSubjectTotal()->willReturn(10000);
        $firstItem->getTotal()->willReturn(6000);
        $firstItem->getQuantity()->willReturn(1);
        $secondItem->getTotal()->willReturn(4000);
        $secondItem->getQuantity()->willReturn(1);
        $promotion->getAppliesToDiscounted()->willReturn(true);

        $minimumPriceDistributor->distribute([$firstItem, $secondItem], -1000, $channel, true)->willReturn([-200, -800]);

        $unitsPromotionAdjustmentsApplicator->apply($order, $promotion, [-200, -800])->shouldBeCalled();

        $this->execute($order, ['WEB_US' => ['amount' => 1000]], $promotion)->shouldReturn(true);
    }

    function it_uses_a_distributor_and_applicator_to_execute_promotion_action_only_for_non_discounted_items(
        ChannelInterface $channel,
        OrderInterface $order,
        OrderItemInterface $firstItem,
        OrderItemInterface $secondItem,
        ProductVariantInterface $firstVariant,
        ProductVariantInterface $secondVariant,
        PromotionInterface $promotion,
        MinimumPriceDistributorInterface $minimumPriceDistributor,
        ProportionalIntegerDistributorInterface $proportionalIntegerDistributor,
        UnitsPromotionAdjustmentsApplicatorInterface $unitsPromotionAdjustmentsApplicator,
    ): void {
        $channel->getCode()->willReturn('WEB_US');

        $order->getCurrencyCode()->willReturn('USD');
        $order->getChannel()->willReturn($channel);
        $order->countItems()->willReturn(2);
        $order
            ->getItems()
            ->willReturn(new ArrayCollection([$firstItem->getWrappedObject(), $secondItem->getWrappedObject()]))
        ;
        $order->getNonDiscountedItemsTotal()->willReturn(6000);

        $promotion->getAppliesToDiscounted()->willReturn(false);

        $firstItem->getTotal()->willReturn(6000);
        $firstItem->getVariant()->willReturn($firstVariant);
        $secondItem->getTotal()->willReturn(4000);
        $secondItem->getVariant()->willReturn($secondVariant);

        $firstVariant->getAppliedPromotionsForChannel($channel)->willReturn([]);
        $secondVariant->getAppliedPromotionsForChannel($channel)->willReturn(['winter_sale' => ['name' => 'Winter sale']]);

        $proportionalIntegerDistributor->distribute([6000, 0], -1000)->shouldNotBeCalled();
        $minimumPriceDistributor->distribute([$firstItem, $secondItem], -1000, $channel, false)->willReturn([-1000, 0]);

        $unitsPromotionAdjustmentsApplicator->apply($order, $promotion, [-1000, 0])->shouldBeCalled();

        $this->execute($order, ['WEB_US' => ['amount' => 1000]], $promotion)->shouldReturn(true);
    }

    function it_does_not_apply_bigger_discount_than_promotion_subject_total(
        ChannelInterface $channel,
        OrderInterface $order,
        OrderItemInterface $firstItem,
        OrderItemInterface $secondItem,
        PromotionInterface $promotion,
        ProportionalIntegerDistributorInterface $proportionalIntegerDistributor,
        UnitsPromotionAdjustmentsApplicatorInterface $unitsPromotionAdjustmentsApplicator,
        MinimumPriceDistributorInterface $minimumPriceDistributor,
        ProductVariantInterface $productVariantOne,
        ProductVariantInterface $productVariantTwo,
        ChannelPricingInterface $channelPricingOne,
        ChannelPricingInterface $channelPricingTwo,
    ): void {
        $channel->getCode()->willReturn('WEB_US');

        $order->getCurrencyCode()->willReturn('USD');
        $order->getChannel()->willReturn($channel);
        $order->countItems()->willReturn(2);
        $order
            ->getItems()
            ->willReturn(new ArrayCollection([$firstItem->getWrappedObject(), $secondItem->getWrappedObject()]))
        ;

        $firstItem->getVariant()->willReturn($productVariantOne);
        $secondItem->getVariant()->willReturn($productVariantTwo);
        $productVariantOne->getChannelPricingForChannel($channel)->willReturn($channelPricingOne);
        $productVariantTwo->getChannelPricingForChannel($channel)->willReturn($channelPricingTwo);

        $channelPricingOne->getMinimumPrice()->willReturn(0);
        $channelPricingTwo->getMinimumPrice()->willReturn(0);

        $order->getPromotionSubjectTotal()->willReturn(10000);

        $promotion->getAppliesToDiscounted()->willReturn(true);

        $firstItem->getTotal()->willReturn(6000);
        $firstItem->getQuantity()->willReturn(1);
        $secondItem->getTotal()->willReturn(4000);
        $secondItem->getQuantity()->willReturn(1);

        $minimumPriceDistributor->distribute([$firstItem, $secondItem], -10000, $channel, true)->willReturn([-6000, -4000]);

        $unitsPromotionAdjustmentsApplicator->apply($order, $promotion, [-6000, -4000])->shouldBeCalled();

        $this->execute($order, ['WEB_US' => ['amount' => 15000]], $promotion)->shouldReturn(true);
    }

    function it_does_not_apply_discount_if_order_has_no_items(
        ChannelInterface $channel,
        OrderInterface $order,
        PromotionInterface $promotion,
    ): void {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');

        $order->countItems()->willReturn(0);
        $order->getPromotionSubjectTotal()->shouldNotBeCalled();

        $this->execute($order, ['WEB_US' => ['amount' => 1000]], $promotion)->shouldReturn(false);
    }

    function it_does_not_apply_discount_if_subject_total_is_0(
        ChannelInterface $channel,
        OrderInterface $order,
        PromotionInterface $promotion,
        MinimumPriceDistributorInterface $minimumPriceDistributor,
    ): void {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');

        $order->countItems()->willReturn(0);
        $order->getPromotionSubjectTotal()->willReturn(0);
        $minimumPriceDistributor->distribute(Argument::any())->shouldNotBeCalled();

        $this->execute($order, ['WEB_US' => ['amount' => 1000]], $promotion)->shouldReturn(false);
    }

    function it_does_not_apply_discount_if_promotion_amount_is_0(
        ChannelInterface $channel,
        OrderInterface $order,
        PromotionInterface $promotion,
        MinimumPriceDistributorInterface $minimumPriceDistributor,
    ): void {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');

        $order->countItems()->willReturn(0);
        $order->getPromotionSubjectTotal()->willReturn(1000);
        $minimumPriceDistributor->distribute(Argument::any())->shouldNotBeCalled();

        $this->execute($order, ['WEB_US' => ['amount' => 0]], $promotion)->shouldReturn(false);
    }

    function it_does_not_apply_discount_if_amount_for_order_channel_is_not_configured(
        ChannelInterface $channel,
        OrderInterface $order,
        PromotionInterface $promotion,
    ): void {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');

        $order->countItems()->willReturn(1);
        $order->getPromotionSubjectTotal()->shouldNotBeCalled();

        $this->execute($order, ['WEB_PL' => ['amount' => 1000]], $promotion)->shouldReturn(false);
    }

    function it_does_not_apply_discount_if_configuration_is_invalid(
        ChannelInterface $channel,
        OrderInterface $order,
        PromotionInterface $promotion,
    ): void {
        $order->getChannel()->willReturn($channel, $channel);
        $channel->getCode()->willReturn('WEB_US', 'WEB_US');
        $order->countItems()->willReturn(1, 1);

        $this->execute($order, ['WEB_US' => []], $promotion)->shouldReturn(false);
        $this->execute($order, ['WEB_US' => ['amount' => 'string']], $promotion)->shouldReturn(false);
    }

    function it_throws_an_exception_if_subject_is_not_an_order(
        PromotionInterface $promotion,
        PromotionSubjectInterface $subject,
    ): void {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('execute', [$subject, [], $promotion])
        ;
    }

    function it_reverts_an_order_units_order_promotion_adjustments(
        AdjustmentInterface $firstAdjustment,
        AdjustmentInterface $secondAdjustment,
        OrderInterface $order,
        OrderItemInterface $item,
        OrderItemUnitInterface $unit,
        PromotionInterface $promotion,
    ): void {
        $order->countItems()->willReturn(1);
        $order->getItems()->willReturn(new ArrayCollection([$item->getWrappedObject()]));

        $item->getUnits()->willReturn(new ArrayCollection([$unit->getWrappedObject()]));

        $unit
            ->getAdjustments(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$firstAdjustment->getWrappedObject(), $secondAdjustment->getWrappedObject()]))
        ;

        $firstAdjustment->getOriginCode()->willReturn('PROMOTION');
        $secondAdjustment->getOriginCode()->willReturn('OTHER_PROMOTION');

        $promotion->getCode()->willReturn('PROMOTION');

        $unit->removeAdjustment($firstAdjustment)->shouldBeCalled();
        $unit->removeAdjustment($secondAdjustment)->shouldNotBeCalled();

        $this->revert($order, [], $promotion);
    }

    function it_does_not_revert_if_order_has_no_items(OrderInterface $order, PromotionInterface $promotion): void
    {
        $order->countItems()->willReturn(0);
        $order->getItems()->shouldNotBeCalled();

        $this->revert($order, [], $promotion);
    }

    function it_throws_an_exception_while_reverting_subject_which_is_not_order(
        PromotionInterface $promotion,
        PromotionSubjectInterface $subject,
    ): void {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('revert', [$subject, [], $promotion])
        ;
    }
}
