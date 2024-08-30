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
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Comparable;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductImagesAwareInterface;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Model\ProductVariant as BaseProductVariant;
use Sylius\Resource\Model\VersionedInterface;
use Sylius\Component\Shipping\Model\ShippableInterface;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Sylius\Component\Taxation\Model\TaxableInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;

final class ProductVariantSpec extends ObjectBehavior
{
    function it_implements_a_product_variant_interface(): void
    {
        $this->shouldImplement(ProductVariantInterface::class);
    }

    function it_implements_a_taxable_interface(): void
    {
        $this->shouldImplement(TaxableInterface::class);
    }

    function it_implements_doctrine_comparable(): void
    {
        $this->shouldImplement(Comparable::class);
    }

    function it_extends_a_product_variant_model(): void
    {
        $this->shouldHaveType(BaseProductVariant::class);
    }

    function it_implements_a_shippable_interface(): void
    {
        $this->shouldImplement(ShippableInterface::class);
    }

    function it_implements_versioned_interface(): void
    {
        $this->shouldImplement(VersionedInterface::class);
    }

    function it_implements_a_product_image_aware_interface(): void
    {
        $this->shouldImplement(ProductImagesAwareInterface::class);
    }

    function it_has_version_1_by_default(): void
    {
        $this->getVersion()->shouldReturn(1);
    }

    function it_has_no_weight_by_default(): void
    {
        $this->getWeight()->shouldReturn(null);
    }

    function its_weight_is_mutable(): void
    {
        $this->setWeight(120.00);
        $this->getWeight()->shouldReturn(120.00);
    }

    function it_has_no_width_by_default(): void
    {
        $this->getWidth()->shouldReturn(null);
    }

    function its_width_is_mutable(): void
    {
        $this->setWidth(15.00);
        $this->getWidth()->shouldReturn(15.00);
    }

    function it_has_no_height_by_default(): void
    {
        $this->getHeight()->shouldReturn(null);
    }

    function its_height_is_mutable(): void
    {
        $this->setHeight(40.00);
        $this->getHeight()->shouldReturn(40.00);
    }

    function it_returns_correct_shipping_weight(): void
    {
        $this->setWeight(140.00);
        $this->getShippingWeight()->shouldReturn(140.00);
    }

    function it_returns_correct_shipping_volume(): void
    {
        $this->setWidth(10.00);
        $this->setHeight(20.00);
        $this->setDepth(10.00);
        $this->getShippingVolume()->shouldReturn(2000.00);
    }

    function it_returns_correct_shipping_width(): void
    {
        $this->setWidth(100.00);
        $this->getShippingWidth()->shouldReturn(100.00);
    }

    function it_returns_correct_shipping_height(): void
    {
        $this->setHeight(110.00);
        $this->getShippingHeight()->shouldReturn(110.00);
    }

    function it_has_no_code_by_default(): void
    {
        $this->getCode()->shouldReturn(null);
    }

    function its_code_is_mutable(): void
    {
        $this->setCode('dummy-sku123');
        $this->getCode()->shouldReturn('dummy-sku123');
    }

    function it_does_not_have_tax_category_by_default(): void
    {
        $this->getTaxCategory()->shouldReturn(null);
    }

    function it_allows_setting_the_tax_category(TaxCategoryInterface $taxCategory): void
    {
        $this->setTaxCategory($taxCategory);
        $this->getTaxCategory()->shouldReturn($taxCategory);
    }

    function it_allows_resetting_the_tax_category(TaxCategoryInterface $taxCategory): void
    {
        $this->setTaxCategory($taxCategory);
        $this->getTaxCategory()->shouldReturn($taxCategory);

        $this->setTaxCategory(null);
        $this->getTaxCategory()->shouldReturn(null);
    }

    function it_has_no_shipping_category_by_default(): void
    {
        $this->getShippingCategory()->shouldReturn(null);
    }

    function its_shipping_category_is_mutable(ShippingCategoryInterface $shippingCategory): void
    {
        $this->setShippingCategory($shippingCategory);
        $this->getShippingCategory()->shouldReturn($shippingCategory);
    }

    function it_adds_and_removes_channel_pricings(ChannelPricingInterface $channelPricing): void
    {
        $channelPricing->getChannelCode()->willReturn('WEB');

        $channelPricing->setProductVariant($this)->shouldBeCalled();
        $this->addChannelPricing($channelPricing);
        $this->hasChannelPricing($channelPricing)->shouldReturn(true);

        $channelPricing->setProductVariant(null)->shouldBeCalled();
        $this->removeChannelPricing($channelPricing);
        $this->hasChannelPricing($channelPricing)->shouldReturn(false);
    }

    function it_has_channel_pricings_collection(
        ChannelPricingInterface $firstChannelPricing,
        ChannelPricingInterface $secondChannelPricing,
    ): void {
        $firstChannelPricing->getChannelCode()->willReturn('WEB');
        $secondChannelPricing->getChannelCode()->willReturn('MOB');

        $firstChannelPricing->setProductVariant($this)->shouldBeCalled();
        $secondChannelPricing->setProductVariant($this)->shouldBeCalled();

        $this->addChannelPricing($firstChannelPricing);
        $this->addChannelPricing($secondChannelPricing);

        $this->getChannelPricings()->shouldBeLike(new ArrayCollection([
            'WEB' => $firstChannelPricing->getWrappedObject(),
            'MOB' => $secondChannelPricing->getWrappedObject(),
        ]));
    }

    function it_checks_if_contains_channel_pricing_for_given_channel(
        ChannelInterface $firstChannel,
        ChannelInterface $secondChannel,
        ChannelPricingInterface $firstChannelPricing,
    ): void {
        $firstChannelPricing->getChannelCode()->willReturn('WEB');
        $firstChannel->getCode()->willReturn('WEB');
        $secondChannel->getCode()->willReturn('MOB');

        $firstChannelPricing->setProductVariant($this)->shouldBeCalled();
        $this->addChannelPricing($firstChannelPricing);

        $firstChannelPricing->getChannelCode()->willReturn($firstChannel);

        $this->hasChannelPricingForChannel($firstChannel)->shouldReturn(true);
        $this->hasChannelPricingForChannel($secondChannel)->shouldReturn(false);
    }

    function it_returns_channel_pricing_for_given_channel(
        ChannelInterface $channel,
        ChannelPricingInterface $channelPricing,
    ): void {
        $channelPricing->getChannelCode()->willReturn('WEB');
        $channel->getCode()->willReturn('WEB');

        $channelPricing->setProductVariant($this)->shouldBeCalled();
        $this->addChannelPricing($channelPricing);

        $channelPricing->getChannelCode()->willReturn($channel);

        $this->getChannelPricingForChannel($channel)->shouldReturn($channelPricing);
    }

    function it_requires_shipping_by_default(): void
    {
        $this->isShippingRequired()->shouldReturn(true);
    }

    function its_shipping_can_be_not_required(): void
    {
        $this->setShippingRequired(false);
        $this->isShippingRequired()->shouldReturn(false);
    }

    function it_initializes_image_collection_by_default(): void
    {
        $this->getImages()->shouldHaveType(Collection::class);
    }

    function it_adds_an_image(ProductImageInterface $image): void
    {
        $this->addImage($image);
        $this->hasImages()->shouldReturn(true);
        $this->hasImage($image)->shouldReturn(true);
    }

    function it_removes_an_image(ProductImageInterface $image): void
    {
        $this->addImage($image);
        $this->removeImage($image);
        $this->hasImage($image)->shouldReturn(false);
    }

    function it_returns_images_by_type(ProductImageInterface $image, Product $product): void
    {
        $image->getType()->willReturn('thumbnail');

        $image->setOwner($product)->shouldBeCalled();
        $image->addProductVariant($this)->shouldBeCalled();

        $this->setProduct($product);
        $this->addImage($image);
        $this->getImagesByType('thumbnail')->shouldBeLike(new ArrayCollection([$image->getWrappedObject()]));
    }

    function it_returns_channel_pricing_applied_promotions(
        ChannelPricingInterface $channelPricing,
        ChannelInterface $channel,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $channel->getCode()->willReturn('CHANNEL_WEB');
        $channelPricing->setProductVariant($this)->shouldBeCalled();
        $channelPricing->getChannelCode()->willReturn('CHANNEL_WEB');
        $channelPricing->getAppliedPromotions()->willReturn(new ArrayCollection([$catalogPromotion->getWrappedObject()]));

        $this->addChannelPricing($channelPricing);

        $this->getAppliedPromotionsForChannel($channel)->shouldBeLike(new ArrayCollection([$catalogPromotion->getWrappedObject()]));
    }

    function it_is_comparable(): void
    {
        $this->setCode('test');

        $otherTaxon = new ProductVariant();
        $otherTaxon->setCode('test');
        $this->compareTo($otherTaxon)->shouldReturn(0);

        $otherTaxon->setCode('other');
        $this->compareTo($otherTaxon)->shouldReturn(1);
    }
}
