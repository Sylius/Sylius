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
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Model\ProductVariant as BaseProductVariant;
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

    function it_does_not_have_price_by_default()
    {
        $this->getPrice()->shouldReturn(null);
    }

    function its_price_should_be_mutable()
    {
        $this->setPrice(499);
        $this->getPrice()->shouldReturn(499);
    }

    function its_price_should_accept_only_integer()
    {
        $this->setPrice(410);
        $this->getPrice()->shouldBeInteger();

        $this->shouldThrow(\InvalidArgumentException::class)->duringSetPrice(4.1 * 100);
        $this->shouldThrow(\InvalidArgumentException::class)->duringSetPrice('410');
        $this->shouldThrow(\InvalidArgumentException::class)->duringSetPrice(round(4.1 * 100));
        $this->shouldThrow(\InvalidArgumentException::class)->duringSetPrice([410]);
        $this->shouldThrow(\InvalidArgumentException::class)->duringSetPrice(new \stdClass());
    }

    function it_implements_a_shippable_interface()
    {
        $this->shouldImplement(ShippableInterface::class);
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
}
