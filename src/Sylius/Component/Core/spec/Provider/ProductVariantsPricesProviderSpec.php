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
use Sylius\Component\Product\Model\OptionInterface;
use Sylius\Component\Product\Model\OptionValueInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ProductVariantsPricesProviderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ProductVariantsPricesProvider::class);
    }

    function it_implements_variants_prices_provider_interface()
    {
        $this->shouldImplement(ProductVariantsPricesProviderInterface::class);
    }

    function it_provides_array_containing_product_variant_options_map_with_corresponding_price(
        OptionInterface $colorOption,
        OptionInterface $sizeOption,
        OptionValueInterface $black,
        OptionValueInterface $large,
        OptionValueInterface $small,
        OptionValueInterface $white,
        ProductInterface $tShirt,
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

        $colorOption->getCode()->willReturn('t_shirt_color');
        $sizeOption->getCode()->willReturn('t_shirt_size');

        $blackSmallTShirt->getOptions()->willReturn([$black, $small]);
        $whiteSmallTShirt->getOptions()->willReturn([$white, $small]);
        $blackLargeTShirt->getOptions()->willReturn([$black, $large]);
        $whiteLargeTShirt->getOptions()->willReturn([$white, $large]);

        $blackSmallTShirt->getPrice()->willReturn(1000);
        $whiteSmallTShirt->getPrice()->willReturn(1500);
        $blackLargeTShirt->getPrice()->willReturn(2000);
        $whiteLargeTShirt->getPrice()->willReturn(2500);

        $black->getOption()->willReturn($colorOption);
        $white->getOption()->willReturn($colorOption);
        $small->getOption()->willReturn($sizeOption);
        $large->getOption()->willReturn($sizeOption);

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
