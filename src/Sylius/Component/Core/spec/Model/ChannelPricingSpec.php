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

namespace spec\Sylius\Component\Core\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ChannelPricingSpec extends ObjectBehavior
{
    function it_implements_channel_pricing_interface(): void
    {
        $this->shouldImplement(ChannelPricingInterface::class);
    }

    function its_channel_code_is_mutable(): void
    {
        $this->setChannelCode('WEB');
        $this->getChannelCode()->shouldReturn('WEB');
    }

    function its_product_variant_is_mutable(ProductVariantInterface $productVariant): void
    {
        $this->setProductVariant($productVariant);
        $this->getProductVariant()->shouldReturn($productVariant);
    }

    function its_price_is_mutable(): void
    {
        $this->setPrice(1000);
        $this->getPrice()->shouldReturn(1000);
    }

    function it_does_not_have_original_price_by_default(): void
    {
        $this->getOriginalPrice()->shouldReturn(null);
    }

    function its_original_price_is_mutable(): void
    {
        $this->setOriginalPrice(2000);
        $this->getOriginalPrice()->shouldReturn(2000);
    }

    function its_price_can_be_reduced(): void
    {
        $this->setPrice(1000);
        $this->setOriginalPrice(2000);
        $this->isPriceReduced()->shouldReturn(true);
    }

    function its_price_is_not_reduced_when_does_not_have_original_price(): void
    {
        $this->setPrice(2000);
        $this->isPriceReduced()->shouldReturn(false);
    }

    function its_price_is_not_reduced_when_original_price_is_same_as_price(): void
    {
        $this->setPrice(2000);
        $this->setOriginalPrice(2000);
        $this->isPriceReduced()->shouldReturn(false);
    }

    function it_price_is_not_reduced_when_original_price_is_smaller_than_price(): void
    {
        $this->setPrice(2000);
        $this->setOriginalPrice(1500);
        $this->isPriceReduced()->shouldReturn(false);
    }

    function it_can_have_promotions_applied(): void
    {
        $this->addAppliedPromotion(['winter_sale' => ['name' => 'Winter sale']]);
        $this->addAppliedPromotion(['extra_sale' => ['name' => 'Extra sale']]);
        $this->getAppliedPromotions()->shouldReturn([
            'winter_sale' => ['name' => 'Winter sale'],
            'extra_sale' => ['name' => 'Extra sale'],
        ]);

        $this->removeAppliedPromotion('winter_sale');
        $this->getAppliedPromotions()->shouldReturn([
            'extra_sale' => ['name' => 'Extra sale'],
        ]);
    }

    function its_has_exclusive_promotion_applied_flag_mutable(): void
    {
        $this->setExclusiveCatalogPromotionApplied(true);
        $this->hasExclusiveCatalogPromotionApplied()->willReturn(true);
    }

    function it_can_clear_applied_promotions(): void
    {
        $this->addAppliedPromotion(['winter_sale' => ['name' => 'Winter sale']]);
        $this->setExclusiveCatalogPromotionApplied(true);
        $this->clearAppliedPromotions();
        $this->getAppliedPromotions()->shouldReturn([]);
        $this->hasExclusiveCatalogPromotionApplied()->shouldReturn(false);
    }
}
