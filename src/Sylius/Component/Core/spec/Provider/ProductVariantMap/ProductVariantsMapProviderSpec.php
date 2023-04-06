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
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantMapProviderInterface;
use Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantsMapProviderInterface;

final class ProductVariantsMapProviderSpec extends ObjectBehavior
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

    function it_implements_product_variants_map_provider_interface(): void
    {
        $this->shouldImplement(ProductVariantsMapProviderInterface::class);
    }

    function it_provider_data_for_all_products_enabled_variants(
        ProductVariantMapProviderInterface $firstProvider,
        ProductVariantMapProviderInterface $secondProvider,
        ProductVariantMapProviderInterface $thirdProvider,
        ProductInterface $product,
        ProductVariantInterface $firstVariant,
        ProductVariantInterface $secondVariant,
        ChannelInterface $channel,
    ): void {
        $product->getEnabledVariants()->willReturn(new ArrayCollection([
            $firstVariant->getWrappedObject(),
            $secondVariant->getWrappedObject(),
        ]));

        $firstProvider->supports($firstVariant, ['channel' => $channel])->willReturn(true);
        $firstProvider->provide($firstVariant, ['channel' => $channel])->willReturn([
            'first-first' => ['some'],
        ]);

        $secondProvider->supports($firstVariant, ['channel' => $channel])->willReturn(false);
        $secondProvider->provide($firstVariant, ['channel' => $channel])->shouldNotBeCalled();

        $thirdProvider->supports($firstVariant, ['channel' => $channel])->willReturn(true);
        $thirdProvider->provide($firstVariant, ['channel' => $channel])->willReturn([
            'first-third' => ['data'],
        ]);

        $firstProvider->supports($secondVariant, ['channel' => $channel])->willReturn(false);
        $firstProvider->provide($secondVariant, ['channel' => $channel])->shouldNotBeCalled();

        $secondProvider->supports($secondVariant, ['channel' => $channel])->willReturn(true);
        $secondProvider->provide($secondVariant, ['channel' => $channel])->willReturn([
            'second-second' => ['more'],
        ]);

        $thirdProvider->supports($secondVariant, ['channel' => $channel])->willReturn(true);
        $thirdProvider->provide($secondVariant, ['channel' => $channel])->willReturn([
            'second-third' => ['data'],
        ]);

        $this->provide($product, ['channel' => $channel])->shouldIterateLike([
            [
                'first-first' => ['some'],
                'first-third' => ['data'],
            ],
            [
                'second-second' => ['more'],
                'second-third' => ['data'],
            ],
        ]);
    }
}
