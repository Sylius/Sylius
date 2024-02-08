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

namespace spec\Sylius\Bundle\CoreBundle\Templating\Helper;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Templating\Helper\Helper;

final class PriceHelperSpec extends ObjectBehavior
{
    function let(ProductVariantPricesCalculatorInterface $productVariantPriceCalculator): void
    {
        $this->beConstructedWith($productVariantPriceCalculator);
    }

    function it_is_helper(): void
    {
        $this->shouldHaveType(Helper::class);
    }

    function it_returns_variant_price_for_channel_given_in_context(
        ChannelInterface $channel,
        ProductVariantInterface $productVariant,
        ProductVariantPricesCalculatorInterface $productVariantPriceCalculator,
    ): void {
        $context = ['channel' => $channel];

        $productVariantPriceCalculator->calculate($productVariant, $context)->willReturn(1000);

        $this->getPrice($productVariant, $context)->shouldReturn(1000);
    }

    function it_throws_invalid_argument_exception_when_channel_key_is_not_present_in_context(
        ProductVariantInterface $productVariant,
        ProductVariantPricesCalculatorInterface $productVariantPriceCalculator,
    ): void {
        $context = ['lennahc' => ''];

        $this->shouldThrow(\InvalidArgumentException::class)->during('getPrice', [$productVariant, $context]);

        $productVariantPriceCalculator->calculate($productVariant, $context)->shouldNotBeCalled();
    }

    function it_returns_variant_original_price_for_channel_given_in_context(
        ChannelInterface $channel,
        ProductVariantInterface $productVariant,
        ProductVariantPricesCalculatorInterface $productVariantPriceCalculator,
    ): void {
        $context = ['channel' => $channel];

        $productVariantPriceCalculator->calculateOriginal($productVariant, $context)->willReturn(1000);

        $this->getOriginalPrice($productVariant, $context)->shouldReturn(1000);
    }

    function it_throws_invalid_argument_exception_when_channel_key_is_not_present_in_context_when_getting_original_price(
        ProductVariantInterface $productVariant,
        ProductVariantPricesCalculatorInterface $productVariantPriceCalculator,
    ): void {
        $context = ['lennahc' => ''];

        $this->shouldThrow(\InvalidArgumentException::class)->during('getOriginalPrice', [$productVariant, $context]);

        $productVariantPriceCalculator->calculateOriginal($productVariant, $context)->shouldNotBeCalled();
    }

    function it_returns_true_if_variant_is_discounted_for_channel_given_in_context(
        ChannelInterface $channel,
        ProductVariantInterface $productVariant,
        ProductVariantPricesCalculatorInterface $productVariantPriceCalculator,
    ): void {
        $context = ['channel' => $channel];

        $productVariantPriceCalculator->calculate($productVariant, $context)->willReturn(950);
        $productVariantPriceCalculator->calculateOriginal($productVariant, $context)->willReturn(1000);

        $this->hasDiscount($productVariant, $context)->shouldReturn(true);
    }

    function it_returns_false_if_variant_is_not_discounted_for_channel_given_in_context(
        ChannelInterface $channel,
        ProductVariantInterface $productVariant,
        ProductVariantPricesCalculatorInterface $productVariantPriceCalculator,
    ): void {
        $context = ['channel' => $channel];

        $productVariantPriceCalculator->calculate($productVariant, $context)->willReturn(1000);
        $productVariantPriceCalculator->calculateOriginal($productVariant, $context)->willReturn(1000);

        $this->hasDiscount($productVariant, $context)->shouldReturn(false);
    }

    function it_throws_invalid_argument_exception_when_channel_key_is_not_present_in_context_when_checking_if_variant_is_discounted(
        ProductVariantInterface $productVariant,
        ProductVariantPricesCalculatorInterface $productVariantPriceCalculator,
    ): void {
        $context = ['lennahc' => ''];

        $this->shouldThrow(\InvalidArgumentException::class)->during('hasDiscount', [$productVariant, $context]);

        $productVariantPriceCalculator->calculate($productVariant, $context)->shouldNotBeCalled();
        $productVariantPriceCalculator->calculateOriginal($productVariant, $context)->shouldNotBeCalled();
    }

    function it_has_name(): void
    {
        $this->getName()->shouldReturn('sylius_calculate_price');
    }
}
