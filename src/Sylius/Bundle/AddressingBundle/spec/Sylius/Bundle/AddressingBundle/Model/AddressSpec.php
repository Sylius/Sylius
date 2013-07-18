<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AddressingBundle\Model;

use PhpSpec\ObjectBehavior;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class AddressSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Model\Address');
    }

    function it_implements_Sylius_address_interface()
    {
        $this->shouldImplement('Sylius\Bundle\AddressingBundle\Model\AddressInterface');
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

    function it_has_no_country_by_default()
    {
        $this->getCountry()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\CountryInterface $country
     */
    function its_country_is_mutable($country)
    {
        $this->setCountry($country);
        $this->getCountry()->shouldReturn($country);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\CountryInterface $country
     */
    function it_allows_to_unset_the_country($country)
    {
        $this->setCountry($country);
        $this->getCountry()->shouldReturn($country);

        $this->setCountry(null);
        $this->getCountry()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\CountryInterface  $country
     * @param Sylius\Bundle\AddressingBundle\Model\ProvinceInterface $province
     */
    function it_unsets_the_province_when_erasing_the_country($country, $province)
    {
        $country->hasProvince($province)->willReturn(true);

        $this->setCountry($country);
        $this->setProvince($province);

        $this->setCountry(null);

        $this->getCountry()->shouldReturn(null);
        $this->getProvince()->shouldReturn(null);
    }

    function it_has_no_province_by_default()
    {
        $this->getProvince()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\ProvinceInterface $province
     */
    function it_throws_exception_if_trying_to_define_province_without_country($province)
    {
        $this
            ->shouldThrow(new \BadMethodCallException('Cannot define province on address without assigned country'))
            ->duringSetProvince($province)
        ;
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\CountryInterface  $country
     * @param Sylius\Bundle\AddressingBundle\Model\ProvinceInterface $province
     */
    function its_province_is_mutable($country, $province)
    {
        $country->hasProvince($province)->willReturn(true);
        $this->setCountry($country);

        $this->setProvince($province);
        $this->getProvince()->shouldReturn($province);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\CountryInterface  $country
     * @param Sylius\Bundle\AddressingBundle\Model\ProvinceInterface $province
     */
    function it_throws_if_trying_to_define_province_which_does_not_belong_to_country($country, $province)
    {
        $country->hasProvince($province)->willReturn(false);
        $this->setCountry($country);

        $country->getName()->willReturn('United States');
        $province->getName()->willReturn('Quebec');

        $expectedExceptionMessage = 'Cannot set province "Quebec", because it does not belong to country "United States"';

        $this
            ->shouldThrow(new \InvalidArgumentException($expectedExceptionMessage))
            ->duringSetProvince($province)
        ;
    }

    function it_is_not_valid_by_default()
    {
        $this->isValid()->shouldReturn(false);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\CountryInterface $country
     */
    function it_is_not_valid_when_no_province_selected_and_country_has_provinces($country)
    {
        $country->hasProvinces()->willReturn(true);

        $this->setCountry($country);

        $this->isValid()->shouldReturn(false);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\CountryInterface $country
     */
    function it_is_valid_if_country_has_no_provinces_and_province_is_not_set($country)
    {
        $country->hasProvinces()->willReturn(false);

        $this->setCountry($country);

        $this->isValid()->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\CountryInterface  $country
     * @param Sylius\Bundle\AddressingBundle\Model\ProvinceInterface $province
     */
    function it_is_valid_if_given_province_belongs_to_selected_country($country, $province)
    {
        $country->hasProvinces()->willReturn(false);
        $country->hasProvince($province)->willReturn(true);

        $this->setCountry($country);
        $this->setProvince($province);

        $this->isValid()->shouldReturn(true);
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
        $this->getCreatedAt()->shouldHaveType('DateTime');
    }

    function its_last_update_time_is_undefined_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\CountryInterface  $country
     * @param Sylius\Bundle\AddressingBundle\Model\ProvinceInterface $province
     */
    function it_has_fluent_interface($country, $province)
    {
        $this->setFirstName('John')->shouldReturn($this);
        $this->setLastName('Doe')->shouldReturn($this);
        $this->setStreet('Foo Street 3-44')->shouldReturn($this);
        $this->setCity('Nashville')->shouldReturn($this);
        $this->setPostcode('53562')->shouldReturn($this);

        $country->hasProvince($province)->willReturn(true);

        $this->setCountry($country)->shouldReturn($this);
        $this->setProvince($province)->shouldReturn($this);
    }
}
