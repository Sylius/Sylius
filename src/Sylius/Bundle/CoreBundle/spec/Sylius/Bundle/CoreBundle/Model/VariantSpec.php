<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Model;

use PhpSpec\ObjectBehavior;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class VariantSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Model\Variant');
    }

    function it_implements_Sylius_core_variant_interface()
    {
        $this->shouldImplement('Sylius\Bundle\CoreBundle\Model\VariantInterface');
    }

    function it_extends_Sylius_product_variant_mapped_superclass()
    {
        $this->shouldHaveType('Sylius\Bundle\VariableProductBundle\Model\Variant');
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

    /**
     * @param Sylius\Bundle\CoreBundle\Model\VariantInterface $masterVariant
     */
    function it_should_inherit_price_from_master_variant($masterVariant)
    {
        $masterVariant->isMaster()->willReturn(true);
        $masterVariant->getAvailableOn()->willReturn(new \DateTime('yesterday'));
        $masterVariant->getPrice()->willReturn(499);

        $this->setDefaults($masterVariant);

        $this->getPrice()->shouldReturn(499);
    }

    function it_implements_Sylius_shippable_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ShippingBundle\Model\ShippableInterface');
    }

    /**
     * @param Sylius\Bundle\CoreBundle\Model\ProductInterface $product
     */
    function it_returns_null_if_product_has_no_shipping_category($product)
    {
        $this->setProduct($product);

        $product->getShippingCategory()->willReturn(null)->shouldBeCalled();
        $this->getShippingCategory()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\Corebundle\Model\ProductInterface $product
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingCategorYInterface      $shippingCategory
     */
    function it_returns_the_product_shipping_category($product, $shippingCategory)
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
