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

namespace spec\Sylius\Component\Core\Calculator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Checker\ProductVariantLowestPriceDisplayCheckerInterface;
use Sylius\Component\Core\Exception\MissingChannelConfigurationException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ProductVariantPriceCalculatorSpec extends ObjectBehavior
{
    function it_implements_product_variant_price_calculator_interface(): void
    {
        $this->shouldImplement(ProductVariantPricesCalculatorInterface::class);
    }

    function it_gets_price_for_product_variant_in_given_channel(
        ChannelInterface $channel,
        ChannelPricingInterface $channelPricing,
        ProductVariantInterface $productVariant,
    ): void {
        $productVariant->getChannelPricingForChannel($channel)->willReturn($channelPricing);
        $channelPricing->getPrice()->willReturn(1000);

        $this->calculate($productVariant, ['channel' => $channel])->shouldReturn(1000);
    }

    function it_throws_a_channel_not_defined_exception_if_there_is_no_channel_pricing(
        ChannelInterface $channel,
        ProductVariantInterface $productVariant,
    ): void {
        $channel->getName()->willReturn('WEB');

        $productVariant->getChannelPricingForChannel($channel)->willReturn(null);
        $productVariant->getDescriptor()->willReturn('Red variant (RED_VARIANT)');

        $this
            ->shouldThrow(MissingChannelConfigurationException::class)
            ->during('calculate', [$productVariant, ['channel' => $channel]])
        ;
    }

    function it_throws_a_channel_not_defined_exception_if_there_is_no_variant_price_for_given_channel(
        ChannelInterface $channel,
        ProductVariantInterface $productVariant,
        ChannelPricingInterface $channelPricing,
    ): void {
        $channel->getName()->willReturn('WEB');

        $productVariant->getChannelPricingForChannel($channel)->willReturn($channelPricing);
        $channelPricing->getPrice()->willReturn(null);
        $productVariant->getDescriptor()->willReturn('Red variant (RED_VARIANT)');

        $this
            ->shouldThrow(MissingChannelConfigurationException::class)
            ->during('calculate', [$productVariant, ['channel' => $channel]])
        ;
    }

    function it_throws_exception_if_no_channel_is_defined_in_configuration(ProductVariantInterface $productVariant): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('calculate', [$productVariant, []])
        ;
    }

    function it_gets_original_price_for_product_variant_in_given_channel(
        ChannelInterface $channel,
        ChannelPricingInterface $channelPricing,
        ProductVariantInterface $productVariant,
    ): void {
        $productVariant->getChannelPricingForChannel($channel)->willReturn($channelPricing);
        $channelPricing->getOriginalPrice()->willReturn(1000);

        $this->calculateOriginal($productVariant, ['channel' => $channel])->shouldReturn(1000);
    }

    function it_gets_price_for_product_variant_if_it_has_no_original_price_in_given_channel(
        ChannelInterface $channel,
        ChannelPricingInterface $channelPricing,
        ProductVariantInterface $productVariant,
    ): void {
        $productVariant->getChannelPricingForChannel($channel)->willReturn($channelPricing);
        $channelPricing->getPrice()->willReturn(1000);
        $channelPricing->getOriginalPrice()->willReturn(null);

        $this->calculateOriginal($productVariant, ['channel' => $channel])->shouldReturn(1000);
    }

    function it_throws_a_channel_not_defined_exception_if_there_is_no_channel_pricing_when_calculating_original_price(
        ChannelInterface $channel,
        ProductVariantInterface $productVariant,
    ): void {
        $channel->getName()->willReturn('WEB');

        $productVariant->getChannelPricingForChannel($channel)->willReturn(null);
        $productVariant->getDescriptor()->willReturn('Red variant (RED_VARIANT)');

        $this
            ->shouldThrow(MissingChannelConfigurationException::class)
            ->during('calculateOriginal', [$productVariant, ['channel' => $channel]])
        ;
    }

    function it_throws_a_channel_not_defined_exception_if_there_is_no_variant_price_for_given_channel_when_calculating_original_price(
        ChannelInterface $channel,
        ProductVariantInterface $productVariant,
        ChannelPricingInterface $channelPricing,
    ): void {
        $channel->getName()->willReturn('WEB');

        $productVariant->getChannelPricingForChannel($channel)->willReturn($channelPricing);
        $channelPricing->getOriginalPrice()->willReturn(null);
        $channelPricing->getPrice()->willReturn(null);
        $productVariant->getDescriptor()->willReturn('Red variant (RED_VARIANT)');

        $this
            ->shouldThrow(MissingChannelConfigurationException::class)
            ->during('calculateOriginal', [$productVariant, ['channel' => $channel]])
        ;
    }

    function it_throws_exception_if_no_channel_is_defined_in_configuration_when_calculating_original_price(ProductVariantInterface $productVariant): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('calculateOriginal', [$productVariant, []])
        ;
    }

    function it_returns_lowest_price_before_discount(
        ProductVariantLowestPriceDisplayCheckerInterface $productVariantLowestPriceDisplayChecker,
        ChannelInterface $channel,
        ChannelPricingInterface $channelPricing,
        ProductVariantInterface $productVariant,
    ): void {
        $this->beConstructedWith($productVariantLowestPriceDisplayChecker);

        $productVariant->getChannelPricingForChannel($channel)->willReturn($channelPricing);
        $productVariantLowestPriceDisplayChecker->isLowestPriceDisplayable($productVariant, ['channel' => $channel])->willReturn(true);

        $channelPricing->getLowestPriceBeforeDiscount()->willReturn(2100);

        $this->calculateLowestPriceBeforeDiscount($productVariant, ['channel' => $channel])->shouldReturn(2100);
    }

    function it_returns_null_when_showing_lowest_price_before_discount_should_not_be_displayed(
        ProductVariantLowestPriceDisplayCheckerInterface $productVariantLowestPriceDisplayChecker,
        ChannelInterface $channel,
        ChannelPricingInterface $channelPricing,
        ProductVariantInterface $productVariant,
    ): void {
        $this->beConstructedWith($productVariantLowestPriceDisplayChecker);

        $productVariant->getChannelPricingForChannel($channel)->willReturn($channelPricing);
        $productVariantLowestPriceDisplayChecker->isLowestPriceDisplayable($productVariant, ['channel' => $channel])->willReturn(false);

        $channelPricing->getLowestPriceBeforeDiscount()->shouldNotBeCalled();

        $this->calculateLowestPriceBeforeDiscount($productVariant, ['channel' => $channel])->shouldReturn(null);
    }

    function it_returns_null_when_is_not_constructed_with_product_variant_lowest_price_display_checker(
        ProductVariantLowestPriceDisplayCheckerInterface $productVariantLowestPriceDisplayChecker,
        ChannelInterface $channel,
        ChannelPricingInterface $channelPricing,
        ProductVariantInterface $productVariant,
    ): void {
        $productVariant->getChannelPricingForChannel($channel)->willReturn($channelPricing);
        $productVariantLowestPriceDisplayChecker
            ->isLowestPriceDisplayable($productVariant, ['channel' => $channel])
            ->shouldNotBeCalled()
        ;

        $channelPricing->getLowestPriceBeforeDiscount()->shouldNotBeCalled();

        $this->calculateLowestPriceBeforeDiscount($productVariant, ['channel' => $channel])->shouldReturn(null);
    }

    function it_throws_a_channel_not_defined_exception_if_there_is_no_channel_pricing_when_providing_lowest_price_before_discount(
        ProductVariantLowestPriceDisplayCheckerInterface $productVariantLowestPriceDisplayChecker,
        ChannelInterface $channel,
        ProductVariantInterface $productVariant,
    ): void {
        $this->beConstructedWith($productVariantLowestPriceDisplayChecker);

        $productVariant->getChannelPricingForChannel($channel)->willReturn(null);
        $productVariant->getDescriptor()->willReturn('Red variant (RED_VARIANT)');
        $channel->getName()->shouldBeCalled();

        $this
            ->shouldThrow(MissingChannelConfigurationException::class)
            ->during('calculateLowestPriceBeforeDiscount', [$productVariant, ['channel' => $channel]])
        ;
    }

    function it_throws_an_exception_if_there_is_no_channel_passed_in_context(
        ProductVariantInterface $productVariant,
    ): void {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('calculateLowestPriceBeforeDiscount', [$productVariant, []])
        ;
    }

    function it_throws_an_exception_if_there_is_no_channel_set_under_the_channel_key_in_context(
        ProductVariantInterface $productVariant,
    ): void {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('calculateLowestPriceBeforeDiscount', [$productVariant, ['channel' => new \stdClass()]])
        ;
    }
}
