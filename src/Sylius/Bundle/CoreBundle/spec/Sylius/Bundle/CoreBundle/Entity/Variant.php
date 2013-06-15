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

use PHPSpec2\ObjectBehavior;

class Variant extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Model\Variant');
    }

    function it_should_implement_Sylius_product_variant_interface()
    {
        $this->shouldImplement('Sylius\Bundle\AssortmentBundle\Model\Variant\VariantInterface');
    }

    function it_should_extend_Sylius_product_variant_mapped_superclass()
    {
        $this->shouldHaveType('Sylius\Bundle\AssortmentBundle\Entity\Variant\Variant');
    }

    function it_should_implement_Sylius_sellable_interface()
    {
        $this->shouldImplement('Sylius\Bundle\SalesBundle\Model\SellableInterface');
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
     * @param Sylius\Bundle\AssortmentBundle\Model\Variant\VariantInterface $masterVariant
     */
    function it_should_inherit_price_from_master_variant($masterVariant)
    {
        $masterVariant->isMaster()->willReturn(true);
        $masterVariant->getAvailableOn()->willReturn(new \DateTime('yesterday'));
        $masterVariant->getPrice()->willReturn(4.99);

        $this->setDefaults($masterVariant);

        $this->getPrice()->shouldReturn(4.99);
    }

    function it_implements_Sylius_shippable_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ShippingBundle\Model\ShippableInterface');
    }

    /**
     * @param Sylius\Bundle\AssortmentBundle\Model\CustomizableProductInterface $product
     */
    function it_returns_null_if_product_has_no_shipping_category($product)
    {
        $this->setProduct($product);

        $product->getShippingCategory()->willReturn(null)->shouldBeCalled();
        $this->getShippingCategory()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\AssortmentBundle\Model\CustomizableProductInterface $product
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingCategorYInterface      $shippingCategory
     */
    function it_returns_the_product_shipping_category($product, $shippingCategory)
    {
        $this->setProduct($product);

        $product->getShippingCategory()->willReturn($shippingCategory)->shouldBeCalled();
        $this->getShippingCategory()->shouldReturn($shippingCategory);
    }
}
