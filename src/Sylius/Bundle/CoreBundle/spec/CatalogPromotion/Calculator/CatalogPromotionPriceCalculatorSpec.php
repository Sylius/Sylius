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
use Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\CatalogPromotionPriceCalculatorInterface;
use Sylius\Component\Core\Exception\ActionBasedPriceCalculatorNotFoundException;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;

final class CatalogPromotionPriceCalculatorSpec extends ObjectBehavior
{
    function let(
        ActionBasedPriceCalculatorInterface $fixedDiscountCalculator,
        ActionBasedPriceCalculatorInterface $percentageDiscountCalculator,
    ): void {
        $this->beConstructedWith([$fixedDiscountCalculator, $percentageDiscountCalculator]);
    }

    function it_implements_catalog_promotion_price_calculator_interface(): void
    {
        $this->shouldImplement(CatalogPromotionPriceCalculatorInterface::class);
    }

    function it_calculates_price_of_channel_pricing_for_given_action_by_a_proper_calculator(
        ActionBasedPriceCalculatorInterface $fixedDiscountCalculator,
        ActionBasedPriceCalculatorInterface $percentageDiscountCalculator,
        CatalogPromotionActionInterface $action,
        ChannelPricingInterface $channelPricing,
    ): void {
        $fixedDiscountCalculator->supports($action)->willReturn(false);
        $percentageDiscountCalculator->supports($action)->willReturn(true);

        $percentageDiscountCalculator->calculate($channelPricing, $action)->willReturn(1000);

        $this->calculate($channelPricing, $action)->shouldReturn(1000);
    }

    function it_throws_an_exception_if_there_is_no_calculator_that_supports_given_action(
        ActionBasedPriceCalculatorInterface $fixedDiscountCalculator,
        ActionBasedPriceCalculatorInterface $percentageDiscountCalculator,
        CatalogPromotionActionInterface $action,
        ChannelPricingInterface $channelPricing,
    ): void {
        $fixedDiscountCalculator->supports($action)->willReturn(false);
        $percentageDiscountCalculator->supports($action)->willReturn(false);

        $this
            ->shouldThrow(ActionBasedPriceCalculatorNotFoundException::class)
            ->during('calculate', [$channelPricing, $action])
        ;
    }
}
