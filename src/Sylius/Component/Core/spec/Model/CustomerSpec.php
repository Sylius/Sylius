<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Model\CustomerInterface;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
final class CustomerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Customer::class);
    }

    function it_implements_a_user_component_interface()
    {
        $this->shouldImplement(CustomerInterface::class);
    }

    function it_has_no_billing_address_by_default()
    {
        $this->getDefaultAddress()->shouldReturn(null);
    }

    function its_addresses_is_collection()
    {
        $this->getAddresses()->shouldHaveType(ArrayCollection::class);
    }

    function it_has_no_addresses_by_default()
    {
        $this->getAddresses()->count()->shouldReturn(0);
    }

    function its_billing_address_is_mutable(AddressInterface $address)
    {
        $this->setDefaultAddress($address);
        $this->getDefaultAddress()->shouldReturn($address);
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
        $this->setDefaultAddress($address);
        $this->hasAddress($address)->shouldReturn(true);
    }
}
