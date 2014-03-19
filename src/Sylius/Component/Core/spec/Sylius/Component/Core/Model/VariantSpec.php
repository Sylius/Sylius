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
use Sylius\Component\Core\Model\VariantInterface;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class VariantSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Model\Variant');
    }

    function it_implements_Sylius_core_variant_interface()
    {
        $this->shouldImplement('Sylius\Component\Core\Model\VariantInterface');
    }

    function it_extends_Sylius_product_variant_mapped_superclass()
    {
        $this->shouldHaveType('Sylius\Component\Product\Model\Variable\Variant');
    }

    function it_should_not_have_price_by_default()
    {
        $this->getPrice()->shouldReturn(null);
    }

    function it_initializes_image_collection_by_default()
    {
        $this->getImages()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    function its_price_should_be_mutable()
    {
        $this->setPrice(4.99)->getPrice()->shouldReturn(4.99);
    }

    function it_should_inherit_price_from_master_variant(VariantInterface $masterVariant)
    {
        $masterVariant->isMaster()->willReturn(true);
        $masterVariant->getAvailableOn()->willReturn(new \DateTime('yesterday'));
        $masterVariant->getPrice()->willReturn(499);

        $this->setDefaults($masterVariant);

        $this->getPrice()->shouldReturn(499);
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
    )
    {
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

    function it_has_no_sku_by_default()
    {
        $this->getSku()->shouldReturn(null);
    }

    function its_sku_is_mutable()
    {
        $sku = 'dummy-sku123';

        $this->setSku($sku);
        $this->getSku()->shouldReturn($sku);
    }
}
