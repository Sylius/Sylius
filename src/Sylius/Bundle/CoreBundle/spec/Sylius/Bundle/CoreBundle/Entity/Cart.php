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

/**
 * Cart spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Cart extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Entity\Cart');
    }

    function it_implements_Sylius_cart_interface()
    {
        $this->shouldImplement('Sylius\Bundle\CoreBundle\Model\CartInterface');
    }

    function it_extends_Sylius_cart_mapped_superclass()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Entity\Cart');
    }

    function it_implements_shippables_aware_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ShippingBundle\Model\ShippablesAwareInterface');
    }

    function it_has_no_shipping_address_by_default()
    {
        $this->getShippingAddress()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\AddressInterface $address
     */
    function its_shipping_address_is_mutable($address)
    {
        $this->setShippingAddress($address);
        $this->getShippingAddress()->shouldReturn($address);
    }

    function it_has_no_billing_address_by_default()
    {
        $this->getBillingAddress()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\AddressInterface $address
     */
    function its_billing_address_is_mutable($address)
    {
        $this->setBillingAddress($address);
        $this->getBillingAddress()->shouldReturn($address);
    }

    function it_has_no_shipping_method_by_default()
    {
        $this->getShippingMethod()->shouldReturn(null);
    }

    function its_shipping_method_is_mutable($method)
    {
        $this->setShippingMethod($method);
        $this->getShippingMethod()->shouldReturn($method);
    }

    function it_has_no_payment_method_by_default()
    {
        $this->getPaymentMethod()->shouldReturn(null);
    }

    function its_payment_method_is_mutable($method)
    {
        $this->setPaymentMethod($method);
        $this->getPaymentMethod()->shouldReturn($method);
    }
}
