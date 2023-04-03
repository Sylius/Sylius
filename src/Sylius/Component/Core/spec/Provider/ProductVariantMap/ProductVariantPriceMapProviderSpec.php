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

final class ProductVariantPriceMapProviderSpec extends ObjectBehavior
{
    function let(ProductVariantPricesCalculatorInterface $calculator): void
    {
        $this->beConstructedWith($calculator);
    }

    function it_implements_product_variant_options_map_data_provider_interface(): void
    {
        $this->shouldImplement(ProductVariantMapProviderInterface::class);
    }

    function it_supports_variants_with_channel_pricing_in_channel(
        ChannelInterface $channel,
        ProductVariantInterface $variantWithoutChannelPricing,
        ProductVariantInterface $variantWithChannelPricing,
        ChannelPricingInterface $channelPricing
    ): void {
        $variantWithChannelPricing->getChannelPricingForChannel($channel)->willReturn($channelPricing);
        $variantWithoutChannelPricing->getChannelPricingForChannel($channel)->willReturn(null);

        $this->supports($variantWithChannelPricing, $channel)->shouldReturn(true);
        $this->supports($variantWithoutChannelPricing, $channel)->shouldReturn(false);
    }

    function it_provides_price_of_variant_in_channel(
        ChannelInterface $channel,
        ProductVariantInterface $variant,
        ChannelPricingInterface $channelPricing,
        ProductVariantPricesCalculatorInterface $calculator
    ): void {
        $variant->getChannelPricingForChannel($channel)->willReturn($channelPricing);
        $calculator->calculate($variant, ['channel' => $channel])->willReturn(1000);

        $this->provide($variant, $channel)->shouldIterateLike([
            'value' => 1000,
            'price' => 1000,
        ]);
    }
}
