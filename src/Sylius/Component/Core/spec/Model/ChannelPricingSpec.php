<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
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

    function it_initializes_catalog_promotions_collection_by_default(): void
    {
        $this->getAppliedPromotions()->shouldHaveType(ArrayCollection::class);
    }

    function it_has_information_about_applied_exclusive_catalog_promotion_applied(
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $catalogPromotion->isExclusive()->willReturn(true);

        $this->addAppliedPromotion($catalogPromotion);
        $this->hasExclusiveCatalogPromotionApplied()->shouldReturn(true);
    }

    function it_can_have_multiple_promotions_applied(
        CatalogPromotionInterface $firstCatalogPromotion,
        CatalogPromotionInterface $secondCatalogPromotion,
    ): void {
        $this->addAppliedPromotion($firstCatalogPromotion);
        $this->addAppliedPromotion($secondCatalogPromotion);

        $this->getAppliedPromotions()->shouldBeLike(new ArrayCollection([
            $firstCatalogPromotion->getWrappedObject(),
            $secondCatalogPromotion->getWrappedObject(),
        ]));
    }

    function it_can_remove_applied_promotion(CatalogPromotionInterface $catalogPromotion): void
    {
        $this->removeAppliedPromotion($catalogPromotion);
        $this->hasPromotionApplied($catalogPromotion)->shouldReturn(false);
    }

    function it_can_clear_applied_promotions(CatalogPromotionInterface $catalogPromotion): void
    {
        $this->addAppliedPromotion($catalogPromotion);
        $this->clearAppliedPromotions();
        $this->getAppliedPromotions()->shouldHaveCount(0);
    }

    function it_can_check_if_given_catalog_promotion_is_applied_or_not(CatalogPromotionInterface $catalogPromotion): void
    {
        $this->addAppliedPromotion($catalogPromotion);
        $this->hasPromotionApplied($catalogPromotion)->shouldReturn(true);

        $this->clearAppliedPromotions();
        $this->getAppliedPromotions()->shouldBeLike(new ArrayCollection());
        $this->hasExclusiveCatalogPromotionApplied()->shouldReturn(false);
        $this->hasPromotionApplied($catalogPromotion)->shouldReturn(false);
    }
}
