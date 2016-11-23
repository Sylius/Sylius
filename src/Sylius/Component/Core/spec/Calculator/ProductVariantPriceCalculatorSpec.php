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

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Calculator\ProductVariantPriceCalculator;
use Sylius\Component\Core\Calculator\ProductVariantPriceCalculatorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ProductVariantPriceCalculatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ProductVariantPriceCalculator::class);
    }

    function it_implements_product_variant_price_calculator_interface()
    {
        $this->shouldImplement(ProductVariantPriceCalculatorInterface::class);
    }

    function it_gets_price_for_product_variant_in_given_channel(
        ChannelInterface $channel,
        ChannelPricingInterface $channelPricing,
        ProductVariantInterface $productVariant
    ) {
        $productVariant->getChannelPricingForChannel($channel)->willReturn($channelPricing);
        $channelPricing->getPrice()->willReturn(1000);

        $this->calculate($productVariant, ['channel' => $channel])->shouldReturn(1000);
    }

    function it_throws_exception_if_there_is_no_variant_price_for_given_channel(
        ChannelInterface $channel,
        ProductVariantInterface $productVariant
    ) {
        $productVariant->getChannelPricingForChannel($channel)->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('calculate', [$productVariant, ['chanel' => $channel]])
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
