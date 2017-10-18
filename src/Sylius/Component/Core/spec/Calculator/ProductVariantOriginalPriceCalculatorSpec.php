<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Calculator;

use Sylius\Component\Core\Calculator\ProductVariantOriginalPriceCalculator;

/**
 * @author Ahmed Kooli <kooliahmd@gmail.com>
 */
class ProductVariantOriginalPriceCalculatorSpec
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ProductVariantOriginalPriceCalculator::class);
    }

    function it_implements_product_variant_price_calculator_interface()
    {
        $this->shouldImplement(ProductVariantPriceCalculatorInterface::class);
    }

    function it_gets_original_price_for_product_variant_in_given_channel(
        ChannelInterface $channel,
        ChannelPricingInterface $channelPricing,
        ProductVariantInterface $productVariant
    ) {
        $productVariant->getChannelPricingForChannel($channel)->willReturn($channelPricing);
        $channelPricing->getOriginalPrice()->willReturn(1000);

        $this->calculate($productVariant, ['channel' => $channel])->shouldReturn(1000);
    }

    function it_throws_a_channel_not_defined_exception_if_there_is_no_variant_price_for_given_channel(
        ChannelInterface $channel,
        ProductVariantInterface $productVariant
    ) {
        $channel->getName()->willReturn('WEB');

        $productVariant->getChannelPricingForChannel($channel)->willReturn(null);
        $productVariant->getName()->willReturn('Red variant');

        $this
            ->shouldThrow(MissingChannelConfigurationException::class)
            ->during('calculate', [$productVariant, ['channel' => $channel]])
        ;
    }

    function it_throws_exception_if_no_channel_is_defined_in_configuration(ProductVariantInterface $productVariant)
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('calculate', [$productVariant, []])
        ;
    }
}
