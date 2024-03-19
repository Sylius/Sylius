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
    function let(ProductVariantPricesCalculatorInterface $productVariantPricesCalculator): void
    {
        $this->beConstructedWith($productVariantPricesCalculator);
    }

    function it_is_helper(): void
    {
        $this->shouldHaveType(Helper::class);
    }

    function it_returns_variant_price_for_channel_given_in_context(
        ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
        ChannelInterface $channel,
        ProductVariantInterface $productVariant,
    ): void {
        $context = ['channel' => $channel];

        $productVariantPricesCalculator->calculate($productVariant, $context)->willReturn(1000);

        $this->getPrice($productVariant, $context)->shouldReturn(1000);
    }

    function it_throws_invalid_argument_exception_when_channel_key_is_not_present_in_context(
        ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
        ProductVariantInterface $productVariant,
    ): void {
        $context = ['lennahc' => ''];

        $this->shouldThrow(\InvalidArgumentException::class)->during('getPrice', [$productVariant, $context]);

        $productVariantPricesCalculator->calculate($productVariant, $context)->shouldNotBeCalled();
    }

    function it_returns_variant_original_price_for_channel_given_in_context(
        ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
        ChannelInterface $channel,
        ProductVariantInterface $productVariant,
    ): void {
        $context = ['channel' => $channel];

        $productVariantPricesCalculator->calculateOriginal($productVariant, $context)->willReturn(1000);

        $this->getOriginalPrice($productVariant, $context)->shouldReturn(1000);
    }

    function it_throws_invalid_argument_exception_when_channel_key_is_not_present_in_context_when_getting_original_price(
        ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
        ProductVariantInterface $productVariant,
    ): void {
        $context = ['lennahc' => ''];

        $this->shouldThrow(\InvalidArgumentException::class)->during('getOriginalPrice', [$productVariant, $context]);

        $productVariantPricesCalculator->calculateOriginal($productVariant, $context)->shouldNotBeCalled();
    }

    function it_returns_true_if_variant_is_discounted_for_channel_given_in_context(
        ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
        ChannelInterface $channel,
        ProductVariantInterface $productVariant,
    ): void {
        $context = ['channel' => $channel];

        $productVariantPricesCalculator->calculate($productVariant, $context)->willReturn(950);
        $productVariantPricesCalculator->calculateOriginal($productVariant, $context)->willReturn(1000);

        $this->hasDiscount($productVariant, $context)->shouldReturn(true);
    }

    function it_returns_false_if_variant_is_not_discounted_for_channel_given_in_context(
        ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
        ChannelInterface $channel,
        ProductVariantInterface $productVariant,
    ): void {
        $context = ['channel' => $channel];

        $productVariantPricesCalculator->calculate($productVariant, $context)->willReturn(1000);
        $productVariantPricesCalculator->calculateOriginal($productVariant, $context)->willReturn(1000);

        $this->hasDiscount($productVariant, $context)->shouldReturn(false);
    }

    function it_throws_invalid_argument_exception_when_channel_key_is_not_present_in_context_when_checking_if_variant_is_discounted(
        ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
        ProductVariantInterface $productVariant,
    ): void {
        $context = ['lennahc' => ''];

        $this->shouldThrow(\InvalidArgumentException::class)->during('hasDiscount', [$productVariant, $context]);

        $productVariantPricesCalculator->calculate($productVariant, $context)->shouldNotBeCalled();
        $productVariantPricesCalculator->calculateOriginal($productVariant, $context)->shouldNotBeCalled();
    }

    function it_has_name(): void
    {
        $this->getName()->shouldReturn('sylius_calculate_price');
    }

    function it_throws_an_exception_if_channel_is_not_provided_when_getting_lowest_price(ProductVariantInterface $productVariant): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getLowestPriceBeforeDiscount', [$productVariant, []])
        ;
    }

    function it_returns_lowest_price_before_discount(
        ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
        ProductVariantInterface $productVariant,
        ChannelInterface $channel,
    ): void {
        $productVariantPricesCalculator
            ->calculateLowestPriceBeforeDiscount($productVariant, ['channel' => $channel])
            ->willReturn(1000)
        ;

        $this
            ->getLowestPriceBeforeDiscount($productVariant, ['channel' => $channel])
            ->shouldReturn(1000)
        ;
    }

    function it_returns_null_when_lowest_price_before_discount_is_unavailable(
        ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
        ProductVariantInterface $productVariant,
        ChannelInterface $channel,
    ): void {
        $productVariantPricesCalculator
            ->calculateLowestPriceBeforeDiscount($productVariant, ['channel' => $channel])
            ->willReturn(null)
        ;

        $this
            ->getLowestPriceBeforeDiscount($productVariant, ['channel' => $channel])
            ->shouldReturn(null)
        ;
    }

    function it_throws_an_exception_if_channel_is_not_provided_when_checking_if_lowest_price_is_available(ProductVariantInterface $productVariant): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('hasLowestPriceBeforeDiscount', [$productVariant, []])
        ;
    }

    function it_returns_true_if_lowest_price_before_discount_is_available(
        ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
        ProductVariantInterface $productVariant,
        ChannelInterface $channel,
    ): void {
        $productVariantPricesCalculator
            ->calculateLowestPriceBeforeDiscount($productVariant, ['channel' => $channel])
            ->willReturn(1000)
        ;

        $this
            ->hasLowestPriceBeforeDiscount($productVariant, ['channel' => $channel])
            ->shouldReturn(true)
        ;
    }

    function it_returns_false_if_lowest_price_before_discount_is_unavailable(
        ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
        ProductVariantInterface $productVariant,
        ChannelInterface $channel,
    ): void {
        $productVariantPricesCalculator
            ->calculateLowestPriceBeforeDiscount($productVariant, ['channel' => $channel])
            ->willReturn(null)
        ;

        $this
            ->hasLowestPriceBeforeDiscount($productVariant, ['channel' => $channel])
            ->shouldReturn(false)
        ;
    }
}
