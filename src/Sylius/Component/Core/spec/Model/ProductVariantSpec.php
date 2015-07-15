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

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ProductVariantSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Model\ProductVariant');
    }

    public function it_implements_Sylius_product_variant_interface()
    {
        $this->shouldImplement('Sylius\Component\Core\Model\ProductVariantInterface');
    }

    public function it_extends_Sylius_product_variant_model()
    {
        $this->shouldHaveType('Sylius\Component\Product\Model\Variant');
    }

    public function it_should_not_have_price_by_default()
    {
        $this->getPrice()->shouldReturn(null);
    }

    public function it_initializes_image_collection_by_default()
    {
        $this->getImages()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    public function its_price_should_be_mutable()
    {
        $this->setPrice(499)->getPrice()->shouldReturn(499);
    }

    public function its_price_should_accept_only_integer()
    {
        $this->setPrice(410)->getPrice()->shouldBeInteger();
        $this->shouldThrow('\InvalidArgumentException')->duringSetPrice(4.1 * 100);
        $this->shouldThrow('\InvalidArgumentException')->duringSetPrice('410');
        $this->shouldThrow('\InvalidArgumentException')->duringSetPrice(round(4.1 * 100));
        $this->shouldThrow('\InvalidArgumentException')->duringSetPrice(array(410));
        $this->shouldThrow('\InvalidArgumentException')->duringSetPrice(new \stdClass());
    }

    public function it_should_inherit_price_from_master_variant(ProductVariantInterface $masterVariant)
    {
        $masterVariant->isMaster()->willReturn(true);
        $masterVariant->getAvailableOn()->willReturn(new \DateTime('yesterday'));
        $masterVariant->getPrice()->willReturn(499);

        $this->setDefaults($masterVariant);

        $this->getPrice()->shouldReturn(499);
    }

    public function it_implements_Sylius_shippable_interface()
    {
        $this->shouldImplement('Sylius\Component\Shipping\Model\ShippableInterface');
    }

    public function it_returns_null_if_product_has_no_shipping_category(ProductInterface $product)
    {
        $this->setProduct($product);

        $product->getShippingCategory()->willReturn(null)->shouldBeCalled();
        $this->getShippingCategory()->shouldReturn(null);
    }

    public function it_returns_the_product_shipping_category(
        ProductInterface $product,
        ShippingCategoryInterface $shippingCategory
    ) {
        $this->setProduct($product);

        $product->getShippingCategory()->willReturn($shippingCategory)->shouldBeCalled();
        $this->getShippingCategory()->shouldReturn($shippingCategory);
    }

    public function it_has_no_weight_by_default()
    {
        $this->getWeight()->shouldReturn(null);
    }

    public function its_weight_is_mutable()
    {
        $this->setWeight(120);
        $this->getWeight()->shouldReturn(120);
    }

    public function it_has_no_width_by_default()
    {
        $this->getWidth()->shouldReturn(null);
    }

    public function its_width_is_mutable()
    {
        $this->setWidth(15);
        $this->getWidth()->shouldReturn(15);
    }

    public function it_has_no_height_by_default()
    {
        $this->getHeight()->shouldReturn(null);
    }

    public function its_height_is_mutable()
    {
        $this->setHeight(40);
        $this->getHeight()->shouldReturn(40);
    }

    public function it_returns_correct_shipping_weight()
    {
        $this->setWeight(140);
        $this->getShippingWeight()->shouldReturn(140);
    }

    public function it_returns_correct_shipping_volume()
    {
        $this->setWidth(10);
        $this->setHeight(20);
        $this->setDepth(10);
        $this->getShippingVolume()->shouldReturn(2000);
    }

    public function it_returns_correct_shipping_width()
    {
        $this->setWidth(100);
        $this->getShippingWidth()->shouldReturn(100);
    }

    public function it_returns_correct_shipping_height()
    {
        $this->setHeight(110);
        $this->getShippingHeight()->shouldReturn(110);
    }

    public function it_has_no_sku_by_default()
    {
        $this->getSku()->shouldReturn(null);
    }

    public function its_sku_is_mutable()
    {
        $sku = 'dummy-sku123';

        $this->setSku($sku);
        $this->getSku()->shouldReturn($sku);
    }
}
