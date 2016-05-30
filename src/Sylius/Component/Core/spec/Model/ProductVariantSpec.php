<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Sylius\Component\Taxation\Model\TaxableInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ProductVariantSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Model\ProductVariant');
    }

    function it_implements_Sylius_product_variant_interface()
    {
        $this->shouldImplement(ProductVariantInterface::class);
    }

    function it_implements_Sylius_taxable_interface()
    {
        $this->shouldImplement(TaxableInterface::class);
    }

    function it_extends_Sylius_product_variant_model()
    {
        $this->shouldHaveType('Sylius\Component\Product\Model\Variant');
    }

    function it_has_metadata_class_identifier()
    {
        $this->getMetadataClassIdentifier()->shouldReturn('ProductVariant');
    }

    function it_should_not_have_price_by_default()
    {
        $this->getPrice()->shouldReturn(null);
    }

    function it_should_not_have_original_price_by_default()
    {
        $this->getOriginalPrice()->shouldReturn(null);
    }

    function it_initializes_image_collection_by_default()
    {
        $this->getImages()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    function its_price_should_be_mutable()
    {
        $this->setPrice(499);
        $this->getPrice()->shouldReturn(499);
    }

    function its_original_price_should_be_mutable()
    {
        $this->setOriginalPrice(399);
        $this->getOriginalPrice()->shouldReturn(399);
    }

    function its_price_should_accept_only_integer()
    {
        $this->setPrice(410);
        $this->getPrice()->shouldBeInteger();
        
        $this->shouldThrow('\InvalidArgumentException')->duringSetPrice(4.1 * 100);
        $this->shouldThrow('\InvalidArgumentException')->duringSetPrice('410');
        $this->shouldThrow('\InvalidArgumentException')->duringSetPrice(round(4.1 * 100));
        $this->shouldThrow('\InvalidArgumentException')->duringSetPrice([410]);
        $this->shouldThrow('\InvalidArgumentException')->duringSetPrice(new \stdClass());
    }

    function its_original_price_should_accept_only_integer()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->duringSetOriginalPrice(3.1 * 100);
        $this->shouldThrow(\InvalidArgumentException::class)->duringSetOriginalPrice('310');
        $this->shouldThrow(\InvalidArgumentException::class)->duringSetOriginalPrice(round(3.1 * 100));
        $this->shouldThrow(\InvalidArgumentException::class)->duringSetOriginalPrice([310]);
        $this->shouldThrow(\InvalidArgumentException::class)->duringSetOriginalPrice(new \stdClass());
    }

    function it_implements_Sylius_shippable_interface()
    {
        $this->shouldImplement('Sylius\Component\Shipping\Model\ShippableInterface');
    }

    function it_returns_null_if_product_has_no_shipping_category(ProductInterface $product)
    {
        $this->setProduct($product);

        $product->getShippingCategory()->willReturn(null)->shouldBeCalled();
        $this->getShippingCategory()->shouldReturn(null);
    }

    function it_returns_the_product_shipping_category(
        ProductInterface $product,
        ShippingCategoryInterface $shippingCategory
    ) {
        $this->setProduct($product);

        $product->getShippingCategory()->willReturn($shippingCategory)->shouldBeCalled();
        $this->getShippingCategory()->shouldReturn($shippingCategory);
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
        $this->getShippingWeight()->shouldReturn(140);
    }

    function it_returns_correct_shipping_volume()
    {
        $this->setWidth(10);
        $this->setHeight(20);
        $this->setDepth(10);
        $this->getShippingVolume()->shouldReturn(2000);
    }

    function it_returns_correct_shipping_width()
    {
        $this->setWidth(100);
        $this->getShippingWidth()->shouldReturn(100);
    }

    function it_returns_correct_shipping_height()
    {
        $this->setHeight(110);
        $this->getShippingHeight()->shouldReturn(110);
    }

    function it_has_no_code_by_default()
    {
        $this->getCode()->shouldReturn(null);
    }

    function its_code_is_mutable()
    {
        $sku = 'dummy-sku123';

        $this->setCode($sku);
        $this->getCode()->shouldReturn($sku);
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
}
