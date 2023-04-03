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

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantMapProviderInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;

final class ProductVariantOptionsMapProviderSpec extends ObjectBehavior
{
    function it_implements_product_variant_options_map_data_provider_interface(): void
    {
        $this->shouldImplement(ProductVariantMapProviderInterface::class);
    }

    function it_supports_variants_with_option_values(
        ChannelInterface $channel,
        ProductVariantInterface $variantWithoutOptions,
        ProductVariantInterface $variantWithOptions,
        ProductOptionValueInterface $optionValue,
    ): void {
        $variantWithOptions->getOptionValues()->willReturn(new ArrayCollection([
            $optionValue->getWrappedObject(),
        ]));
        $variantWithoutOptions->getOptionValues()->willReturn(new ArrayCollection());

        $this->supports($variantWithOptions, $channel)->shouldReturn(true);
        $this->supports($variantWithoutOptions, $channel)->shouldReturn(false);
    }

    function it_provides_a_map_of_variant_options(
        ChannelInterface $channel,
        ProductVariantInterface $variant,
        ProductOptionValueInterface $firstOptionValue,
        ProductOptionValueInterface $secondOptionValue,
    ): void {
        $firstOptionValue->getOptionCode()->willReturn('first_option');
        $firstOptionValue->getCode()->willReturn('first_option_value');
        $secondOptionValue->getOptionCode()->willReturn('second_option');
        $secondOptionValue->getCode()->willReturn('second_option_value');

        $variant->getOptionValues()->willReturn(new ArrayCollection([
            $firstOptionValue->getWrappedObject(),
            $secondOptionValue->getWrappedObject(),
        ]));

        $this->provide($variant, $channel)->shouldIterateLike([
            'first_option' => 'first_option_value',
            'second_option' => 'second_option_value',
        ]);
    }
}
