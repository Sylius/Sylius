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
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Customer\Model\AddressInterface;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class UserSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Model\User');
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

    function its_shipping_address_is_mutable(AddressInterface $address, CustomerInterface $customer)
    {
        $this->setCustomer($customer);
        $this->setShippingAddress($address);

        $customer->hasAddress($address)->willReturn(true);

        $this->getShippingAddress()->shouldReturn($address);
    }

    function its_billing_address_is_mutable(AddressInterface $address, CustomerInterface $customer)
    {
        $this->setCustomer($customer);
        $this->setBillingAddress($address);

        $customer->hasAddress($address)->willReturn(true);

        $this->getBillingAddress()->shouldReturn($address);
    }
}
