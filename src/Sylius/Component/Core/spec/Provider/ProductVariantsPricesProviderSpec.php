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

namespace spec\Sylius\Component\Core\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Provider\ProductVariantsPricesProviderInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;

final class ProductVariantsPricesProviderSpec extends ObjectBehavior
{
    function let(ProductVariantPricesCalculatorInterface $productVariantPriceCalculator): void
    {
        $this->beConstructedWith($productVariantPriceCalculator);
    }

    function it_implements_a_variants_prices_provider_interface(): void
    {
        $this->shouldImplement(ProductVariantsPricesProviderInterface::class);
    }

    function it_provides_array_containing_product_variant_options_map_with_corresponding_price(
        ChannelInterface $channel,
        ProductInterface $tShirt,
        ProductOptionValueInterface $black,
        ProductOptionValueInterface $large,
        ProductOptionValueInterface $small,
        ProductOptionValueInterface $white,
        ProductVariantInterface $blackLargeTShirt,
        ProductVariantInterface $blackSmallTShirt,
        ProductVariantInterface $whiteLargeTShirt,
        ProductVariantInterface $whiteSmallTShirt,
        ProductVariantPricesCalculatorInterface $productVariantPriceCalculator
    ): void {
        $tShirt->getVariants()->willReturn(new ArrayCollection([
            $blackSmallTShirt->getWrappedObject(),
            $whiteSmallTShirt->getWrappedObject(),
            $blackLargeTShirt->getWrappedObject(),
            $whiteLargeTShirt->getWrappedObject(),
        ]));

        $blackSmallTShirt->getOptionValues()->willReturn(
            new ArrayCollection([$black->getWrappedObject(), $small->getWrappedObject()])
        );
        $whiteSmallTShirt->getOptionValues()->willReturn(
            new ArrayCollection([$white->getWrappedObject(), $small->getWrappedObject()])
        );
        $blackLargeTShirt->getOptionValues()->willReturn(
            new ArrayCollection([$black->getWrappedObject(), $large->getWrappedObject()])
        );
        $whiteLargeTShirt->getOptionValues()->willReturn(
            new ArrayCollection([$white->getWrappedObject(), $large->getWrappedObject()])
        );

        $productVariantPriceCalculator->calculate($blackSmallTShirt, ['channel' => $channel])->willReturn(1000);
        $productVariantPriceCalculator->calculateOriginal($blackSmallTShirt, ['channel' => $channel])->willReturn(1000);
        $productVariantPriceCalculator->calculate($whiteSmallTShirt, ['channel' => $channel])->willReturn(1500);
        $productVariantPriceCalculator->calculateOriginal($whiteSmallTShirt, ['channel' => $channel])->willReturn(2000);
        $productVariantPriceCalculator->calculate($blackLargeTShirt, ['channel' => $channel])->willReturn(2000);
        $productVariantPriceCalculator->calculateOriginal($blackLargeTShirt, ['channel' => $channel])->willReturn(2000);
        $productVariantPriceCalculator->calculate($whiteLargeTShirt, ['channel' => $channel])->willReturn(2500);
        $productVariantPriceCalculator->calculateOriginal($whiteLargeTShirt, ['channel' => $channel])->willReturn(3000);

        $black->getOptionCode()->willReturn('t_shirt_color');
        $white->getOptionCode()->willReturn('t_shirt_color');
        $small->getOptionCode()->willReturn('t_shirt_size');
        $large->getOptionCode()->willReturn('t_shirt_size');

        $black->getCode()->willReturn('black');
        $white->getCode()->willReturn('white');
        $small->getCode()->willReturn('small');
        $large->getCode()->willReturn('large');

        $this->provideVariantsPrices($tShirt, $channel)->shouldReturn([
            [
                't_shirt_color' => 'black',
                't_shirt_size' => 'small',
                'value' => 1000,
            ],
            [
                't_shirt_color' => 'white',
                't_shirt_size' => 'small',
                'value' => 1500,
                'original-price' => 2000,
            ],
            [
                't_shirt_color' => 'black',
                't_shirt_size' => 'large',
                'value' => 2000,
            ],
            [
                't_shirt_color' => 'white',
                't_shirt_size' => 'large',
                'value' => 2500,
                'original-price' => 3000,
            ],
        ]);
    }
}
