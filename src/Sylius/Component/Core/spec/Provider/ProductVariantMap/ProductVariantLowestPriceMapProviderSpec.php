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

namespace spec\Sylius\Component\Core\Provider\ProductVariantMap;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantMapProviderInterface;

final class ProductVariantLowestPriceMapProviderSpec extends ObjectBehavior
{
    function let(ProductVariantPricesCalculatorInterface $calculator): void
    {
        $this->beConstructedWith($calculator);
    }

    function it_implements_product_variant_options_map_data_provider_interface(): void
    {
        $this->shouldImplement(ProductVariantMapProviderInterface::class);
    }

    function it_does_not_support_context_with_no_channel(
        ProductVariantInterface $variant,
    ): void {
        $this->supports($variant, [])->shouldReturn(false);
    }

    function it_does_not_support_context_with_channel_that_is_not_a_channel_interface(
        ProductVariantInterface $variant,
    ): void {
        $this->supports($variant, ['channel' => 'not_a_channel'])->shouldReturn(false);
    }

    function it_supports_variants_with_lowest_price_in_channel(
        ProductVariantPricesCalculatorInterface $calculator,
        ChannelInterface $channel,
        ProductVariantInterface $variantWithoutChannelPricing,
        ProductVariantInterface $variantWithChannelPricing,
        ChannelPricingInterface $channelPricing,
    ): void {
        $variantWithoutChannelPricing->getChannelPricingForChannel($channel)->willReturn(null);
        $variantWithChannelPricing->getChannelPricingForChannel($channel)->willReturn($channelPricing);
        $calculator->calculateLowestPriceBeforeDiscount($variantWithChannelPricing, ['channel' => $channel])->willReturn(1000);

        $this->supports($variantWithChannelPricing, ['channel' => $channel])->shouldReturn(true);
        $this->supports($variantWithoutChannelPricing, ['channel' => $channel])->shouldReturn(false);
    }

    function it_provides_lowest_price_of_variant_in_channel(
        ProductVariantPricesCalculatorInterface $calculator,
        ChannelInterface $channel,
        ProductVariantInterface $variant,
    ): void {
        $calculator->calculateLowestPriceBeforeDiscount($variant, ['channel' => $channel])->willReturn(1000);

        $this->provide($variant, ['channel' => $channel])->shouldIterateLike([
            'lowest-price-before-discount' => 1000,
        ]);
    }
}
