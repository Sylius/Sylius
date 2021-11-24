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

namespace spec\Sylius\Bundle\CoreBundle\Calculator;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Calculator\ActionBasedPriceCalculatorInterface;
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
        CatalogPromotionActionInterface $percentageDiscountAction
    ): void {
        $fixedDiscountAction->getType()->willReturn(CatalogPromotionActionInterface::TYPE_FIXED_DISCOUNT);
        $percentageDiscountAction->getType()->willReturn(CatalogPromotionActionInterface::TYPE_PERCENTAGE_DISCOUNT);

        $this->supports($fixedDiscountAction)->shouldReturn(false);
        $this->supports($percentageDiscountAction)->shouldReturn(true);
    }

    function it_calculates_price_for_given_channel_pricing_and_action(
        ChannelPricingInterface $channelPricing,
        CatalogPromotionActionInterface $action
    ): void {
        $action->getConfiguration()->willReturn(['amount' => 0.3]);

        $channelPricing->getPrice()->willReturn(1000);
        $channelPricing->getMinimumPrice()->willReturn(0);

        $this->calculate($channelPricing, $action)->shouldReturn(700);
    }
}
