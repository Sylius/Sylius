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

namespace spec\Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\ActionBasedPriceCalculatorInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\FixedDiscountPriceCalculator;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\PercentageDiscountPriceCalculator;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;

final class PercentageDiscountPriceCalculatorSpec extends ObjectBehavior
{
    function it_implements_action_based_price_calculator_interface(): void
    {
        $this->shouldImplement(ActionBasedPriceCalculatorInterface::class);
    }

    function it_supports_only_percentage_discount_catalog_promotion_action(
        CatalogPromotionActionInterface $fixedDiscountAction,
        CatalogPromotionActionInterface $percentageDiscountAction,
    ): void {
        $fixedDiscountAction->getType()->willReturn(FixedDiscountPriceCalculator::TYPE);
        $percentageDiscountAction->getType()->willReturn(PercentageDiscountPriceCalculator::TYPE);

        $this->supports($fixedDiscountAction)->shouldReturn(false);
        $this->supports($percentageDiscountAction)->shouldReturn(true);
    }

    function it_calculates_price_for_given_channel_pricing_and_action(
        ChannelPricingInterface $channelPricing,
        CatalogPromotionActionInterface $action,
    ): void {
        $action->getConfiguration()->willReturn(['amount' => 0.3]);

        $channelPricing->getPrice()->willReturn(1000);
        $channelPricing->getMinimumPrice()->willReturn(0);

        $this->calculate($channelPricing, $action)->shouldReturn(700);
    }

    function it_calculates_and_rounds_price_for_given_channel_pricing_and_action(
        ChannelPricingInterface $channelPricing,
        CatalogPromotionActionInterface $action,
    ): void {
        $action->getConfiguration()->willReturn(['amount' => 0.3]);

        $channelPricing->getPrice()->willReturn(951);
        $channelPricing->getMinimumPrice()->willReturn(0);

        $this->calculate($channelPricing, $action)->shouldReturn(666);
    }

    function it_calculates_price_for_given_channel_pricing_and_action_with_taking_minimum_price_into_account(
        ChannelPricingInterface $channelPricing,
        CatalogPromotionActionInterface $action,
    ): void {
        $action->getConfiguration()->willReturn(['amount' => 0.7]);

        $channelPricing->getPrice()->willReturn(1000);
        $channelPricing->getMinimumPrice()->willReturn(500);

        $this->calculate($channelPricing, $action)->shouldReturn(500);
    }
}
