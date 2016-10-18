<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Provider\ProductVariantsPricesProvider;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Provider\ProductVariantsPricesProviderInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ProductVariantsPricesProviderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ProductVariantsPricesProvider::class);
    }

    function it_implements_a_variants_prices_provider_interface()
    {
        $this->shouldImplement(ProductVariantsPricesProviderInterface::class);
    }

    function it_provides_array_containing_product_variant_options_map_with_corresponding_price(
        ProductInterface $tShirt,
        ProductOptionValueInterface $black,
        ProductOptionValueInterface $large,
        ProductOptionValueInterface $small,
        ProductOptionValueInterface $white,
        ProductVariantInterface $blackLargeTShirt,
        ProductVariantInterface $blackSmallTShirt,
        ProductVariantInterface $whiteLargeTShirt,
        ProductVariantInterface $whiteSmallTShirt
    ) {
        $tShirt->getVariants()->willReturn([
            $blackSmallTShirt,
            $whiteSmallTShirt,
            $blackLargeTShirt,
            $whiteLargeTShirt
        ]);

        $blackSmallTShirt->getOptionValues()->willReturn([$black, $small]);
        $whiteSmallTShirt->getOptionValues()->willReturn([$white, $small]);
        $blackLargeTShirt->getOptionValues()->willReturn([$black, $large]);
        $whiteLargeTShirt->getOptionValues()->willReturn([$white, $large]);

        $blackSmallTShirt->getPrice()->willReturn(1000);
        $whiteSmallTShirt->getPrice()->willReturn(1500);
        $blackLargeTShirt->getPrice()->willReturn(2000);
        $whiteLargeTShirt->getPrice()->willReturn(2500);

        $black->getOptionCode()->willReturn('t_shirt_color');
        $white->getOptionCode()->willReturn('t_shirt_color');
        $small->getOptionCode()->willReturn('t_shirt_size');
        $large->getOptionCode()->willReturn('t_shirt_size');

        $black->getValue()->willReturn('Black');
        $white->getValue()->willReturn('White');
        $small->getValue()->willReturn('Small');
        $large->getValue()->willReturn('Large');

        $this->provideVariantsPrices($tShirt)->shouldReturn([
            [
                't_shirt_color' => 'Black',
                't_shirt_size' => 'Small',
                'value' => 1000,
            ],
            [
                't_shirt_color' => 'White',
                't_shirt_size' => 'Small',
                'value' => 1500,
            ],
            [
                't_shirt_color' => 'Black',
                't_shirt_size' => 'Large',
                'value' => 2000,
            ],
            [
                't_shirt_color' => 'White',
                't_shirt_size' => 'Large',
                'value' => 2500,
            ]
        ]);
    }
}
