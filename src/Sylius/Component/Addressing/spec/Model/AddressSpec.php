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
use Sylius\Component\Addressing\Model\Address;
use Sylius\Component\Addressing\Model\AddressInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class AddressSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Address::class);
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
        $this->getCountryCode()->shouldReturn(null);
    }

    function its_country_code_is_mutable()
    {
        $this->setCountryCode('IE');
        $this->getCountryCode()->shouldReturn('IE');
    }

    function it_allows_to_unset_the_country_code()
    {
        $this->setCountryCode('IE');
        $this->getCountryCode()->shouldReturn('IE');

        $this->setCountryCode(null);
        $this->getCountryCode()->shouldReturn(null);
    }

    function it_unsets_the_province_code_when_erasing_country_code()
    {
        $this->setCountryCode('IE');
        $this->setProvinceCode('DU');

        $this->setCountryCode(null);

        $this->getCountryCode()->shouldReturn(null);
        $this->getProvinceCode()->shouldReturn(null);
    }

    function it_has_no_province_code_by_default()
    {
        $this->getProvinceCode()->shouldReturn(null);
    }

    function it_ignores_province_code_when_there_is_no_country_code()
    {
        $this->setCountryCode(null);
        $this->setProvinceCode('DU');
        $this->getProvinceCode()->shouldReturn(null);
    }

    function its_province_code_is_mutable()
    {
        $this->setCountryCode('IE');

        $this->setProvinceCode('DU');
        $this->getProvinceCode()->shouldReturn('DU');
    }

    function it_has_no_province_name_by_default()
    {
        $this->getProvinceName()->shouldReturn(null);
    }

    function its_province_name_is_mutable()
    {
        $this->setProvinceName('Utah');
        $this->getProvinceName()->shouldReturn('Utah');
    }

    function it_has_no_company_by_default()
    {
        $this->getCompany()->shouldReturn(null);
    }

    function its_company_is_mutable()
    {
        $this->setCompany('Foo Ltd.');
        $this->getCompany()->shouldReturn('Foo Ltd.');
    }

    function it_has_no_street_by_default()
    {
        $this->getStreet()->shouldReturn(null);
    }

    function its_street_is_mutable()
    {
        $this->setStreet('Foo Street 3/44');
        $this->getStreet()->shouldReturn('Foo Street 3/44');
    }

    function it_has_no_city_by_default()
    {
        $this->getCity()->shouldReturn(null);
    }

    function its_city_is_mutable()
    {
        $this->setCity('New York');
        $this->getCity()->shouldReturn('New York');
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
