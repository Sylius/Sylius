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
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantMapProviderInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;

final class ProductVariantOptionsMapProviderSpec extends ObjectBehavior
{
    function it_implements_product_variant_options_map_data_provider_interface(): void
    {
        $this->shouldImplement(ProductVariantMapProviderInterface::class);
    }

    function it_does_not_support_variants_with_no_option_values(ProductVariantInterface $variant): void
    {
        $variant->getOptionValues()->willReturn(new ArrayCollection());

        $this->supports($variant, [])->shouldReturn(false);
    }

    function it_supports_variants_with_option_values(
        ProductVariantInterface $variant,
        ProductOptionValueInterface $optionValue,
    ): void {
        $variant->getOptionValues()->willReturn(new ArrayCollection([
            $optionValue->getWrappedObject(),
        ]));

        $this->supports($variant, [])->shouldReturn(true);
    }

    function it_provides_a_map_of_variant_options(
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

        $this->provide($variant, [])->shouldIterateLike([
            'first_option' => 'first_option_value',
            'second_option' => 'second_option_value',
        ]);
    }
}
