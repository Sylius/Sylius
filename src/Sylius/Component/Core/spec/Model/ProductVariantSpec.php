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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductImagesAwareInterface;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Model\ProductVariant as BaseProductVariant;
use Sylius\Component\Resource\Model\VersionedInterface;
use Sylius\Component\Shipping\Model\ShippableInterface;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Sylius\Component\Taxation\Model\TaxableInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ProductVariantSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ProductVariant::class);
    }

    function it_implements_a_product_variant_interface()
    {
        $this->shouldImplement(ProductVariantInterface::class);
    }

    function it_implements_a_taxable_interface()
    {
        $this->shouldImplement(TaxableInterface::class);
    }

    function it_extends_a_product_variant_model()
    {
        $this->shouldHaveType(BaseProductVariant::class);
    }

    function it_implements_a_shippable_interface()
    {
        $this->shouldImplement(ShippableInterface::class);
    }

    function it_implements_versioned_interface()
    {
        $this->shouldImplement(VersionedInterface::class);
    }

    function it_implements_a_product_image_aware_interface()
    {
        $this->shouldImplement(ProductImagesAwareInterface::class);
    }

    function it_has_version_1_by_default()
    {
        $this->getVersion()->shouldReturn(1);
    }

    function it_has_no_weight_by_default()
    {
        $this->getWeight()->shouldReturn(null);
    }

    function its_weight_is_mutable()
    {
        $this->setWeight(120);
        $this->getWeight()->shouldReturn(120);
    }

    function it_has_no_width_by_default()
    {
        $this->getWidth()->shouldReturn(null);
    }

    function its_width_is_mutable()
    {
        $this->setWidth(15);
        $this->getWidth()->shouldReturn(15);
    }

    function it_has_no_height_by_default()
    {
        $this->getHeight()->shouldReturn(null);
    }

    function its_height_is_mutable()
    {
        $this->setHeight(40);
        $this->getHeight()->shouldReturn(40);
    }

    function it_returns_correct_shipping_weight()
    {
        $this->setWeight(140);
        $this->getShippingWeight()->shouldReturn(140.00);
    }

    function it_returns_correct_shipping_volume()
    {
        $this->setWidth(10);
        $this->setHeight(20);
        $this->setDepth(10);
        $this->getShippingVolume()->shouldReturn(2000.00);
    }

    function it_returns_correct_shipping_width()
    {
        $this->setWidth(100);
        $this->getShippingWidth()->shouldReturn(100.00);
    }

    function it_returns_correct_shipping_height()
    {
        $this->setHeight(110);
        $this->getShippingHeight()->shouldReturn(110.00);
    }

    function it_has_no_code_by_default()
    {
        $this->getCode()->shouldReturn(null);
    }

    function its_code_is_mutable()
    {
        $this->setCode('dummy-sku123');
        $this->getCode()->shouldReturn('dummy-sku123');
    }

    function it_does_not_have_tax_category_by_default()
    {
        $this->getTaxCategory()->shouldReturn(null);
    }

    function it_allows_setting_the_tax_category(TaxCategoryInterface $taxCategory)
    {
        $this->setTaxCategory($taxCategory);
        $this->getTaxCategory()->shouldReturn($taxCategory);
    }

    function it_allows_resetting_the_tax_category(TaxCategoryInterface $taxCategory)
    {
        $this->setTaxCategory($taxCategory);
        $this->getTaxCategory()->shouldReturn($taxCategory);

        $this->setTaxCategory(null);
        $this->getTaxCategory()->shouldReturn(null);
    }

    function it_has_no_shipping_category_by_default()
    {
        $this->getShippingCategory()->shouldReturn(null);
    }

    function its_shipping_category_is_mutable(ShippingCategoryInterface $shippingCategory)
    {
        $this->setShippingCategory($shippingCategory);
        $this->getShippingCategory()->shouldReturn($shippingCategory);
    }

    function it_adds_and_removes_channel_pricings(ChannelPricingInterface $channelPricing)
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
        ChannelPricingInterface $secondChannelPricing
    ) {
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
        ChannelPricingInterface $firstChannelPricing
    ) {
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
        ChannelPricingInterface $channelPricing
    ) {
        $channelPricing->getChannelCode()->willReturn('WEB');
        $channel->getCode()->willReturn('WEB');

        $channelPricing->setProductVariant($this)->shouldBeCalled();
        $this->addChannelPricing($channelPricing);

        $channelPricing->getChannelCode()->willReturn($channel);

        $this->getChannelPricingForChannel($channel)->shouldReturn($channelPricing);
    }

    function it_requires_shipping_by_default()
    {
        $this->isShippingRequired()->shouldReturn(true);
    }

    function its_shipping_can_be_not_required()
    {
        $this->setShippingRequired(false);
        $this->isShippingRequired()->shouldReturn(false);
    }

    function it_initializes_image_collection_by_default()
    {
        $this->getImages()->shouldHaveType(Collection::class);
    }

    function it_adds_an_image(ProductImageInterface $image)
    {
        $this->addImage($image);
        $this->hasImages()->shouldReturn(true);
        $this->hasImage($image)->shouldReturn(true);
    }

    function it_removes_an_image(ProductImageInterface $image)
    {
        $this->addImage($image);
        $this->removeImage($image);
        $this->hasImage($image)->shouldReturn(false);
    }

    function it_returns_images_by_type(ProductImageInterface $image, Product $product)
    {
        $image->getType()->willReturn('thumbnail');

        $image->setOwner($product)->shouldBeCalled();
        $image->addProductVariant($this)->shouldBeCalled();

        $this->setProduct($product);
        $this->addImage($image);
        $this->getImagesByType('thumbnail')->shouldBeLike(new ArrayCollection([$image->getWrappedObject()]));
    }
}
