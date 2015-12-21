<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Addressing\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\AdministrativeAreaInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class AddressSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Addressing\Model\Address');
    }

    function it_implements_Sylius_address_interface()
    {
        $this->shouldImplement(AddressInterface::class);
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_first_name_by_default()
    {
        $this->getFirstName()->shouldReturn(null);
    }

    function its_first_name_is_mutable()
    {
        $this->setFirstName('John');
        $this->getFirstName()->shouldReturn('John');
    }

    function it_has_no_last_name_by_default()
    {
        $this->getLastName()->shouldReturn(null);
    }

    function its_last_name_is_mutable()
    {
        $this->setLastName('Doe');
        $this->getLastName()->shouldReturn('Doe');
    }

    function it_returns_correct_full_name()
    {
        $this->setFirstName('John');
        $this->setLastName('Doe');

        $this->getFullName()->shouldReturn('John Doe');
    }

    function it_has_no_phone_number_by_default()
    {
        $this->getPhoneNumber()->shouldReturn(null);
    }

    function its_phone_number_is_mutable()
    {
        $this->setPhoneNumber('+48555123456');
        $this->getPhoneNumber()->shouldReturn('+48555123456');
    }

    function it_has_no_country_by_default()
    {
        $this->getCountry()->shouldReturn(null);
    }

    function its_country_is_mutable()
    {
        $this->setCountry('IE');
        $this->getCountry()->shouldReturn('IE');
    }

    function it_nullifies_the_administrative_area_when_erasing_the_country()
    {
        $this->setCountry('IE');
        $this->setAdministrativeArea('DUB');

        $this->setCountry(null);
        $this->getAdministrativeArea()->shouldReturn(null);
    }

    function it_has_no_administrative_area_by_default()
    {
        $this->getAdministrativeArea()->shouldReturn(null);
    }

    function its_administrative_area_is_mutable()
    {
        $this->setCountry('IE');

        $this->setAdministrativeArea('DUB');
        $this->getAdministrativeArea()->shouldReturn('DUB');
    }

    function it_has_no_organization_by_default()
    {
        $this->getOrganization()->shouldReturn(null);
    }

    function its_organization_is_mutable()
    {
        $this->setOrganization('Foo Ltd.');
        $this->getOrganization()->shouldReturn('Foo Ltd.');
    }

    function it_has_no_locality_by_default()
    {
        $this->getLocality()->shouldReturn(null);
    }

    function its_locality_is_mutable()
    {
        $this->setLocality('Dublin');
        $this->getLocality()->shouldReturn('Dublin');
    }

    function it_has_no_dependent_locality_by_default()
    {
        $this->getDependentLocality()->shouldReturn(null);
    }

    function its_dependent_locality_is_mutable()
    {
        $this->setDependentLocality('Temple Bar');
        $this->getDependentLocality()->shouldReturn('Temple Bar');
    }

    function it_has_no_first_address_line_by_default()
    {
        $this->getFirstAddressLine()->shouldReturn(null);
    }

    function its_first_address_line_is_mutable()
    {
        $this->setFirstAddressLine('Blakley Street');
        $this->getFirstAddressLine()->shouldReturn('Blakley Street');
    }

    function it_has_no_second_address_line_by_default()
    {
        $this->getSecondAddressLine()->shouldReturn(null);
    }

    function its_second_address_line_is_mutable()
    {
        $this->setSecondAddressLine('3/16A');
        $this->getSecondAddressLine()->shouldReturn('3/16A');
    }

    function it_has_no_postcode_by_default()
    {
        $this->getPostcode()->shouldReturn(null);
    }

    function its_postcode_is_mutable()
    {
        $this->setPostcode('24154');
        $this->getPostcode()->shouldReturn('24154');
    }

    function its_creation_time_is_initialized_by_default()
    {
        $this->getCreatedAt()->shouldHaveType(\DateTime::class);
    }

    function its_last_update_time_is_undefined_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }
}
