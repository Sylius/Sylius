<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Entity;

use PHPSpec2\ObjectBehavior;
use Sylius\Bundle\ShippingBundle\Model\ShipmentItemInterface;

/**
 * Cart item spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CartItem extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Entity\CartItem');
    }

    function it_implements_Sylius_cart_item_interface()
    {
        $this->shouldImplement('Sylius\Bundle\CartBundle\Model\CartItemInterface');
    }

    function it_extends_Sylius_cart_item_mapped_superclass()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Entity\CartItem');
    }

    function it_has_no_product_variant_assigned_by_default()
    {
        $this->getVariant()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\AssortmentBundle\Model\Variant\VariantInterface $variant
     */
    function it_allows_setting_the_product_variant($variant)
    {
        $this->setVariant($variant);
        $this->getVariant()->shouldReturn($variant);
    }

    /**
     * @param Sylius\Bundle\AssortmentBundle\Model\Variant\VariantInterface $variantA
     * @param Sylius\Bundle\AssortmentBundle\Model\Variant\VariantInterface $variantB
     * @param Sylius\Bundle\CoreBundle\Entity\CartItem                   $cartItem
     */
    function it_should_be_equal_to_item_with_same_variant($variantA, $variantB, $cartItem)
    {
        $variantA->getId()->willReturn(3);
        $variantB->getId()->willReturn(3);

        $cartItem->getVariant()->willReturn($variantB);
        $this->setVariant($variantA);

        $this->equals($cartItem)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\AssortmentBundle\Model\Variant\VariantInterface $variantA
     * @param Sylius\Bundle\AssortmentBundle\Model\Variant\VariantInterface $variantB
     * @param Sylius\Bundle\CoreBundle\Entity\CartItem                   $cartItem
     */
    function it_should_not_be_equal_to_item_with_different_variant($variantA, $variantB, $cartItem)
    {
        $variantA->getId()->willReturn(3);
        $variantB->getId()->willReturn(6);

        $cartItem->getVariant()->willReturn($variantB);
        $this->setVariant($variantA);

        $this->equals($cartItem)->shouldReturn(false);
    }
}
