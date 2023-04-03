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
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantMapProviderInterface;

final class ProductVariantMapProviderSpec extends ObjectBehavior
{
    function let(
        ProductVariantMapProviderInterface $firstProvider,
        ProductVariantMapProviderInterface $secondProvider,
        ProductVariantMapProviderInterface $thirdProvider,
    ): void {
        $this->beConstructedWith([
            $firstProvider,
            $secondProvider,
            $thirdProvider,
        ]);
    }

    function it_implements_product_variant_options_map_data_provider_interface(): void
    {
        $this->shouldImplement(ProductVariantMapProviderInterface::class);
    }

    function it_supports_all_variants(
        ProductVariantInterface $variant,
        ChannelInterface $channel,
    ): void {
        $this->supports($variant, $channel)->shouldReturn(true);
    }

    function it_provides_data_from_all_supported_providers(
        ProductVariantInterface $variant,
        ChannelInterface $channel,
        ProductVariantMapProviderInterface $firstProvider,
        ProductVariantMapProviderInterface $secondProvider,
        ProductVariantMapProviderInterface $thirdProvider,
    ): void {
        $firstProvider->supports($variant, $channel)->willReturn(true);
        $firstProvider->provide($variant, $channel)->willReturn([
            'first' => ['some', 'more', 'data'],
        ]);

        $secondProvider->supports($variant, $channel)->willReturn(false);
        $secondProvider->provide($variant, $channel)->shouldNotBeCalled();

        $thirdProvider->supports($variant, $channel)->willReturn(true);
        $thirdProvider->provide($variant, $channel)->willReturn(['third' => 'third']);

        $this->provide($variant, $channel)->shouldIterateLike([
            'first' => ['some', 'more', 'data'],
            'third' => 'third',
        ]);
    }
}
