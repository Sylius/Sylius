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

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;

/**

 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class CustomerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Model\Customer');
    }

    function it_implements_user_component_interface()
    {
        $this->shouldImplement(CustomerInterface::class);
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
        $this->getAddresses()->shouldHaveType(ArrayCollection::class);
    }

    function it_has_no_addresses_by_default()
    {
        $this->getAddresses()->count()->shouldReturn(0);
    }

    function its_shipping_address_is_mutable(AddressInterface $address)
    {
        $this->setShippingAddress($address);
        $this->getShippingAddress()->shouldReturn($address);
    }

    function its_billing_address_is_mutable(AddressInterface $address)
    {
        $this->setBillingAddress($address);
        $this->getBillingAddress()->shouldReturn($address);
    }

    function its_addresses_are_mutable(AddressInterface $address)
    {
        $this->addAddress($address);
        $this->hasAddress($address)->shouldReturn(true);
    }

    function it_can_remove_addresses(AddressInterface $address)
    {
        $this->addAddress($address);
        $this->removeAddress($address);
        $this->hasAddress($address)->shouldReturn(false);
    }

    function it_adds_address_when_billing_address_is_set(AddressInterface $address)
    {
        $this->setBillingAddress($address);
        $this->hasAddress($address)->shouldReturn(true);
    }

    function it_adds_address_when_shipping_address_is_set(AddressInterface $address)
    {
        $this->setShippingAddress($address);
        $this->hasAddress($address)->shouldReturn(true);
    }
}