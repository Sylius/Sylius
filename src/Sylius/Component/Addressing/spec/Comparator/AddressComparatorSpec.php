<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Addressing\Comparator;

use Sylius\Component\Addressing\Comparator\AddressComparator;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Comparator\AddressComparatorInterface;
use Sylius\Component\Addressing\Model\AddressInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class AddressComparatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(AddressComparator::class);
    }

    function it_implements_address_comparator_interface()
    {
        $this->shouldImplement(AddressComparatorInterface::class);
    }

    function it_returns_false_if_addresses_differ(AddressInterface $firstAddress, AddressInterface $secondAddress)
    {
        $firstAddress->getCity()->willReturn('Stoke-On-Trent');
        $firstAddress->getStreet()->willReturn('Villiers St');
        $firstAddress->getCompany()->willReturn('Pizzeria');
        $firstAddress->getPostcode()->willReturn('ST3 4HB');
        $firstAddress->getLastName()->willReturn('Johnson');
        $firstAddress->getFirstName()->willReturn('Gerald');
        $firstAddress->getPhoneNumber()->willReturn('000');
        $firstAddress->getCountryCode()->willReturn('UK');
        $firstAddress->getProvinceCode()->willReturn('UK-WestMidlands');
        $firstAddress->getProvinceName()->willReturn(null);

        $secondAddress->getCity()->willReturn('Toowoomba');
        $secondAddress->getStreet()->willReturn('Ryans Dr');
        $secondAddress->getCompany()->willReturn('Burger');
        $secondAddress->getPostcode()->willReturn('4350');
        $secondAddress->getLastName()->willReturn('Jones');
        $secondAddress->getFirstName()->willReturn('Mia');
        $secondAddress->getPhoneNumber()->willReturn('999');
        $secondAddress->getCountryCode()->willReturn('AU');
        $secondAddress->getProvinceCode()->willReturn(null);
        $secondAddress->getProvinceName()->willReturn('Queensland');

        $this->equal($firstAddress, $secondAddress)->shouldReturn(false);
    }

    function it_returns_true_when_addresses_are_the_same(AddressInterface $address)
    {
        $address->getCity()->willReturn('Toowoomba');
        $address->getStreet()->willReturn('Ryans Dr');
        $address->getCompany()->willReturn('Burger');
        $address->getPostcode()->willReturn('4350');
        $address->getLastName()->willReturn('Jones');
        $address->getFirstName()->willReturn('Mia');
        $address->getPhoneNumber()->willReturn('999');
        $address->getCountryCode()->willReturn('AU');
        $address->getProvinceCode()->willReturn(null);
        $address->getProvinceName()->willReturn('Queensland');

        $this->equal($address, $address)->shouldReturn(true);
    }

    function it_ignores_leading_and_trailing_spaces_or_letter_cases(AddressInterface $firstAddress, AddressInterface $secondAddress)
    {
        $firstAddress->getCity()->willReturn('TOOWOOMBA');
        $firstAddress->getStreet()->willReturn('Ryans Dr     ');
        $firstAddress->getCompany()->willReturn('   Burger');
        $firstAddress->getPostcode()->willReturn(' 4350 ');
        $firstAddress->getLastName()->willReturn('jones ');
        $firstAddress->getFirstName()->willReturn('mIa');
        $firstAddress->getPhoneNumber()->willReturn(' 999');
        $firstAddress->getCountryCode()->willReturn('au');
        $firstAddress->getProvinceCode()->willReturn(null);
        $firstAddress->getProvinceName()->willReturn('qUEENSLAND');

        $secondAddress->getCity()->willReturn('Toowoomba');
        $secondAddress->getStreet()->willReturn('Ryans Dr');
        $secondAddress->getCompany()->willReturn('Burger');
        $secondAddress->getPostcode()->willReturn('4350');
        $secondAddress->getLastName()->willReturn('Jones');
        $secondAddress->getFirstName()->willReturn('Mia');
        $secondAddress->getPhoneNumber()->willReturn('999');
        $secondAddress->getCountryCode()->willReturn('AU');
        $secondAddress->getProvinceCode()->willReturn(null);
        $secondAddress->getProvinceName()->willReturn('Queensland');

        $this->equal($firstAddress, $secondAddress)->shouldReturn(true);
    }
}
