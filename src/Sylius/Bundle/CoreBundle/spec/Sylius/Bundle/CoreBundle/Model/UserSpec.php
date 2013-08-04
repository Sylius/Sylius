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

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;

/**

 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class UserSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Model\User');
    }

    function it_implements_Fos_user_interface()
    {
        $this->shouldImplement('FOS\UserBundle\Model\UserInterface');
    }

    function it_has_no_shipping_address_by_default()
    {
        $this->getShippingAddress()->shouldReturn(null);
    }

    function it_has_no_billing_address_by_default()
    {
        $this->getBillingAddress()->shouldReturn(null);
    }

    function its_addresses_is_collection()
    {
        $this->getAddresses()->shouldHaveType('Doctrine\Common\Collections\ArrayCollection');
    }

    function it_has_no_addresses_by_default()
    {
        $this->getAddresses()->count()->shouldReturn(0);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\AddressInterface $address
     */
    function its_shipping_address_is_mutable($address)
    {
        $this->setShippingAddress($address);
        $this->getShippingAddress()->shouldReturn($address);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\AddressInterface $address
     */
    function its_billing_address_is_mutable($address)
    {
        $this->setBillingAddress($address);
        $this->getBillingAddress()->shouldReturn($address);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\AddressInterface $address
     */
    function its_addresses_are_mutable($address)
    {
        $this->addAddress($address);
        $this->hasAddress($address)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\AddressInterface $address
     */
    function it_can_remove_addresses($address)
    {
        $this->addAddress($address);
        $this->removeAddress($address);
        $this->hasAddress($address)->shouldReturn(false);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\AddressInterface $address
     */
    function it_adds_address_when_billing_address_is_set($address)
    {
        $this->setBillingAddress($address);
        $this->hasAddress($address)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\AddressInterface $address
     */
    function it_adds_address_when_shipping_address_is_set($address)
    {
        $this->setShippingAddress($address);
        $this->hasAddress($address)->shouldReturn(true);
    }
}
