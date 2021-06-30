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

namespace spec\Sylius\Bundle\ApiBundle\Mapper;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AddressInterface;

final class AddressMapperSpec extends ObjectBehavior
{
    function it_updates_an_address_with_a_province(
        AddressInterface $currentAddress,
        AddressInterface $targetAddress
    ): void {
        $targetAddress->getFirstName()->willReturn('John');
        $targetAddress->getLastName()->willReturn('Doe');
        $targetAddress->getCompany()->willReturn('CocaCola');
        $targetAddress->getStreet()->willReturn('Green Avenue');
        $targetAddress->getCountryCode()->willReturn('US');
        $targetAddress->getCity()->willReturn('New York');
        $targetAddress->getPostcode()->willReturn('00000');
        $targetAddress->getPhoneNumber()->willReturn('123456789');
        $targetAddress->getProvinceCode()->willReturn('999');
        $targetAddress->getProvinceName()->willReturn('east');

        $currentAddress->setFirstName('John')->shouldBeCalled();
        $currentAddress->setLastName('Doe')->shouldBeCalled();
        $currentAddress->setCompany('CocaCola')->shouldBeCalled();
        $currentAddress->setStreet('Green Avenue')->shouldBeCalled();
        $currentAddress->setCountryCode('US')->shouldBeCalled();
        $currentAddress->setCity('New York')->shouldBeCalled();
        $currentAddress->setPostcode('00000')->shouldBeCalled();
        $currentAddress->setPhoneNumber('123456789')->shouldBeCalled();
        $currentAddress->setProvinceCode('999')->shouldBeCalled();
        $currentAddress->setProvinceName('east')->shouldBeCalled();

        $this->mapExisting($currentAddress, $targetAddress)->shouldReturn($currentAddress);
    }

    function it_updates_an_address_without_a_province(
        AddressInterface $currentAddress,
        AddressInterface $targetAddress
    ): void {
        $targetAddress->getFirstName()->willReturn('John');
        $targetAddress->getLastName()->willReturn('Doe');
        $targetAddress->getCompany()->willReturn('CocaCola');
        $targetAddress->getStreet()->willReturn('Green Avenue');
        $targetAddress->getCountryCode()->willReturn('US');
        $targetAddress->getCity()->willReturn('New York');
        $targetAddress->getPostcode()->willReturn('00000');
        $targetAddress->getPhoneNumber()->willReturn('123456789');
        $targetAddress->getProvinceCode()->willReturn(null);
        $targetAddress->getProvinceName()->willReturn('east')->shouldNotBeCalled();

        $currentAddress->setFirstName('John')->shouldBeCalled();
        $currentAddress->setLastName('Doe')->shouldBeCalled();
        $currentAddress->setCompany('CocaCola')->shouldBeCalled();
        $currentAddress->setStreet('Green Avenue')->shouldBeCalled();
        $currentAddress->setCountryCode('US')->shouldBeCalled();
        $currentAddress->setCity('New York')->shouldBeCalled();
        $currentAddress->setPostcode('00000')->shouldBeCalled();
        $currentAddress->setPhoneNumber('123456789')->shouldBeCalled();
        $currentAddress->setProvinceCode('999')->shouldNotBeCalled();
        $currentAddress->setProvinceName('east')->shouldNotBeCalled();

        $this->mapExisting($currentAddress, $targetAddress)->shouldReturn($currentAddress);
    }
}
