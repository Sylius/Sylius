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

final class PercentageDiscountPromotionActionCommandSpec extends ObjectBehavior
{
    function let(
        ProportionalIntegerDistributorInterface $distributor,
        UnitsPromotionAdjustmentsApplicatorInterface $unitsPromotionAdjustmentsApplicator,
        MinimumPriceDistributorInterface $minimumPriceDistributor
    ): void {
        $this->beConstructedWith($distributor, $unitsPromotionAdjustmentsApplicator, $minimumPriceDistributor);
    }

    function it_implements_a_promotion_action_interface(): void
    {
        $this->shouldImplement(PromotionActionCommandInterface::class);
    }

    function it_uses_distributor_and_applicator_to_execute_promotion_action(
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
        ChannelInterface $channel
    ): void {
        $order->countItems()->willReturn(2);
        $order->getChannel()->willReturn($channel);

        $order->getItems()->willReturn(new ArrayCollection([$firstItem->getWrappedObject(), $secondItem->getWrappedObject()]));

        $firstItem->getTotal()->willReturn(200);
        $firstItem->getQuantity()->willReturn(1);
        $secondItem->getTotal()->willReturn(800);
        $secondItem->getQuantity()->willReturn(1);

        $firstItem->getVariant()->willReturn($productVariantOne);
        $secondItem->getVariant()->willReturn($productVariantTwo);
        $productVariantOne->getChannelPricingForChannel($channel)->willReturn($channelPricingOne);
        $productVariantTwo->getChannelPricingForChannel($channel)->willReturn($channelPricingTwo);

        $channelPricingOne->getMinimumPrice()->willReturn(0);
        $channelPricingTwo->getMinimumPrice()->willReturn(0);

        $order->getPromotionSubjectTotal()->willReturn(10000);

        $minimumPriceDistributor->distribute([$firstItem, $secondItem], -1000, $channel)->willReturn([-200, -800]);
        $unitsPromotionAdjustmentsApplicator->apply($order, $promotion, [-200, -800])->shouldBeCalled();

        $this->execute($order, ['percentage' => 0.1], $promotion)->shouldReturn(true);
    }

    function it_distributes_promotion_using_regular_distributor_if_minimum_price_distributor_is_not_provided(
        OrderInterface $order,
        OrderItemInterface $firstItem,
        OrderItemInterface $secondItem,
        PromotionInterface $promotion,
        ProportionalIntegerDistributorInterface $distributor,
        MinimumPriceDistributorInterface $minimumPriceDistributor,
        UnitsPromotionAdjustmentsApplicatorInterface $unitsPromotionAdjustmentsApplicator,
        ProductVariantInterface $productVariantOne,
        ProductVariantInterface $productVariantTwo,
        ChannelPricingInterface $channelPricingOne,
        ChannelPricingInterface $channelPricingTwo,
        ChannelInterface $channel
    ): void {
        $this->beConstructedWith($distributor, $unitsPromotionAdjustmentsApplicator);

        $order->countItems()->willReturn(2);
        $order->getChannel()->willReturn($channel);

        $order->getItems()->willReturn(new ArrayCollection([$firstItem->getWrappedObject(), $secondItem->getWrappedObject()]));

        $firstItem->getTotal()->willReturn(200);
        $firstItem->getQuantity()->willReturn(1);
        $secondItem->getTotal()->willReturn(800);
        $secondItem->getQuantity()->willReturn(1);

        $firstItem->getVariant()->willReturn($productVariantOne);
        $secondItem->getVariant()->willReturn($productVariantTwo);
        $productVariantOne->getChannelPricingForChannel($channel)->willReturn($channelPricingOne);
        $productVariantTwo->getChannelPricingForChannel($channel)->willReturn($channelPricingTwo);

        $channelPricingOne->getMinimumPrice()->willReturn(0);
        $channelPricingTwo->getMinimumPrice()->willReturn(0);

        $order->getPromotionSubjectTotal()->willReturn(10000);

        $minimumPriceDistributor->distribute([$firstItem, $secondItem], -1000, $channel)->shouldNotBeCalled();
        $distributor->distribute([200, 800], -1000)->willReturn([-200, -800]);

        $unitsPromotionAdjustmentsApplicator->apply($order, $promotion, [-200, -800])->shouldBeCalled();

        $this->execute($order, ['percentage' => 0.1], $promotion)->shouldReturn(true);
    }

    function it_distributes_promotion_amount_taking_minimum_price_into_account(
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
        ChannelInterface $channel
    ): void {
        $order->countItems()->willReturn(2);
        $order->getChannel()->willReturn($channel);

        $order->getItems()->willReturn(new ArrayCollection([$firstItem->getWrappedObject(), $secondItem->getWrappedObject()]));

        $firstItem->getTotal()->willReturn(200);
        $firstItem->getQuantity()->willReturn(1);
        $secondItem->getTotal()->willReturn(800);
        $secondItem->getQuantity()->willReturn(1);

        $firstItem->getVariant()->willReturn($productVariantOne);
        $secondItem->getVariant()->willReturn($productVariantTwo);
        $productVariantOne->getChannelPricingForChannel($channel)->willReturn($channelPricingOne);
        $productVariantTwo->getChannelPricingForChannel($channel)->willReturn($channelPricingTwo);

        $channelPricingOne->getMinimumPrice()->willReturn(100);
        $channelPricingTwo->getMinimumPrice()->willReturn(0);

        $order->getPromotionSubjectTotal()->willReturn(10000);

        $minimumPriceDistributor->distribute([$firstItem, $secondItem], -1000, $channel)->willReturn([-100, -800]);

        $unitsPromotionAdjustmentsApplicator->apply($order, $promotion, [-100, -800])->shouldBeCalled();

        $this->execute($order, ['percentage' => 0.1], $promotion)->shouldReturn(true);
    }

    function it_does_not_apply_discount_if_order_has_no_items(OrderInterface $order, PromotionInterface $promotion): void
    {
        $order->countItems()->willReturn(0);
        $order->getPromotionSubjectTotal()->shouldNotBeCalled();

        $this->execute($order, ['percentage' => 0.1], $promotion)->shouldReturn(false);
    }

    function it_does_not_apply_discount_if_adjustment_amount_would_be_0(
        OrderInterface $order,
        PromotionInterface $promotion,
        MinimumPriceDistributorInterface $minimumPriceDistributor
    ): void {
        $order->countItems()->willReturn(0);

        $order->getPromotionSubjectTotal()->willReturn(0);
        $minimumPriceDistributor->distribute(Argument::any())->shouldNotBeCalled();

        $this->execute($order, ['percentage' => 0.1], $promotion)->shouldReturn(false);
    }

    function it_does_not_apply_discount_if_configuration_is_invalid(
        OrderInterface $order,
        PromotionInterface $promotion
    ): void {
        $order->countItems()->willReturn(1);

        $this->execute($order, [], $promotion)->shouldReturn(false);
        $this->execute($order, ['percentage' => 'string'], $promotion)->shouldReturn(false);
    }

    function it_throws_exception_if_subject_is_not_an_order(
        PromotionInterface $promotion,
        PromotionSubjectInterface $subject
    ): void {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('execute', [$subject, [], $promotion])
        ;
    }

    function it_reverts_order_units_order_promotion_adjustments(
        AdjustmentInterface $firstAdjustment,
        AdjustmentInterface $secondAdjustment,
        OrderInterface $order,
        OrderItemInterface $item,
        OrderItemUnitInterface $unit,
        PromotionInterface $promotion
    ): void {
        $order->countItems()->willReturn(1);
        $order->getItems()->willReturn(new ArrayCollection([$item->getWrappedObject()]));

        $item->getUnits()->willReturn(new ArrayCollection([$unit->getWrappedObject()]));

        $unit
            ->getAdjustments(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$firstAdjustment->getWrappedObject(), $secondAdjustment->getWrappedObject()]))
        ;

        $promotion->getCode()->willReturn('PROMOTION');

        $firstAdjustment->getOriginCode()->willReturn('PROMOTION');
        $secondAdjustment->getOriginCode()->willReturn('OTHER_PROMOTION');

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
        PromotionSubjectInterface $subject
    ): void {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('revert', [$subject, [], $promotion])
        ;
    }
}
