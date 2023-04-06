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
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantMapProviderInterface;

final class ProductVariantAppliedPromotionsMapProviderSpec extends ObjectBehavior
{
    function it_implements_product_variant_options_map_data_provider_interface(): void
    {
        $this->shouldImplement(ProductVariantMapProviderInterface::class);
    }

    function it_does_not_support_context_with_no_channel(
        ProductVariantInterface $variant,
    ): void {
        $this->supports($variant, [])->shouldReturn(false);
    }

    function it_does_not_support_context_with_channel_that_is_not_a_channel_interface(
        ProductVariantInterface $variant,
    ): void {
        $this->supports($variant, ['channel' => 'not_a_channel'])->shouldReturn(false);
    }

    function it_supports_variants_with_applied_promotions(
        ChannelInterface $channel,
        ProductVariantInterface $variantWithoutPromotions,
        ProductVariantInterface $variantWithPromotions,
        PromotionInterface $promotion,
    ): void {
        $variantWithPromotions->getAppliedPromotionsForChannel($channel)->willReturn(new ArrayCollection([
            $promotion->getWrappedObject(),
        ]));
        $variantWithoutPromotions->getAppliedPromotionsForChannel($channel)->willReturn(new ArrayCollection());

        $this->supports($variantWithPromotions, ['channel' => $channel])->shouldReturn(true);
        $this->supports($variantWithoutPromotions, ['channel' => $channel])->shouldReturn(false);
    }

    function it_provides_a_map_of_variant_applied_promotions(
        ChannelInterface $channel,
        ProductVariantInterface $variant,
        PromotionInterface $firstPromotion,
        PromotionInterface $secondPromotion,
    ): void {
        $variant->getAppliedPromotionsForChannel($channel)->willReturn(new ArrayCollection([
            $firstPromotion->getWrappedObject(),
            $secondPromotion->getWrappedObject(),
        ]));

        $this->provide($variant, ['channel' => $channel])->shouldIterateLike([
            'applied_promotions' => [
                $firstPromotion,
                $secondPromotion,
            ],
        ]);
    }
}
