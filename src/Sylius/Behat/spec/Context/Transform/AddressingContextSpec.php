<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Converter\CountryNameConverterInterface;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class AddressingContextSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $addressFactory,
        CountryNameConverterInterface $countryNameConverter
    ) {
        $this->beConstructedWith($addressFactory, $countryNameConverter);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Transform\AddressingContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_creates_new_address_based_on_given_country(
        AddressInterface $address,
        FactoryInterface $addressFactory,
        CountryNameConverterInterface $countryNameConverter
    ) {
        $countryNameConverter->convertToCode('France')->willReturn('FR');

        $addressFactory->createNew()->willReturn($address);

        $address->setCountryCode('FR')->shouldBeCalled();
        $address->setFirstName('John')->shouldBeCalled();
        $address->setLastName('Doe')->shouldBeCalled();
        $address->setCity('Ankh Morpork')->shouldBeCalled();
        $address->setStreet('Frost Alley')->shouldBeCalled();
        $address->setPostcode('90210')->shouldBeCalled();

        $this->createNewAddress('France')->shouldReturn($address);
    }
}
