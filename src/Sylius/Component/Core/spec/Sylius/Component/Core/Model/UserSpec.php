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

use Sylius\Component\Addressing\Model\AddressInterface;

/**

 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class UserSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Model\User');
    }

    public function it_implements_Fos_user_interface()
    {
        $this->shouldImplement('FOS\UserBundle\Model\UserInterface');
    }

    public function it_has_no_shipping_address_by_default()
    {
        $this->getShippingAddress()->shouldReturn(null);
    }

    public function it_has_no_billing_address_by_default()
    {
        $this->getBillingAddress()->shouldReturn(null);
    }

    public function its_addresses_is_collection()
    {
        $this->getAddresses()->shouldHaveType('Doctrine\Common\Collections\ArrayCollection');
    }

    public function it_has_no_addresses_by_default()
    {
        $this->getAddresses()->count()->shouldReturn(0);
    }

    public function its_shipping_address_is_mutable(AddressInterface $address)
    {
        $this->setShippingAddress($address);
        $this->getShippingAddress()->shouldReturn($address);
    }

    public function its_billing_address_is_mutable(AddressInterface $address)
    {
        $this->setBillingAddress($address);
        $this->getBillingAddress()->shouldReturn($address);
    }

    public function its_addresses_are_mutable(AddressInterface $address)
    {
        $this->addAddress($address);
        $this->hasAddress($address)->shouldReturn(true);
    }

    public function it_can_remove_addresses(AddressInterface $address)
    {
        $this->addAddress($address);
        $this->removeAddress($address);
        $this->hasAddress($address)->shouldReturn(false);
    }

    public function it_adds_address_when_billing_address_is_set(AddressInterface $address)
    {
        $this->setBillingAddress($address);
        $this->hasAddress($address)->shouldReturn(true);
    }

    public function it_adds_address_when_shipping_address_is_set(AddressInterface $address)
    {
        $this->setShippingAddress($address);
        $this->hasAddress($address)->shouldReturn(true);
    }
}
