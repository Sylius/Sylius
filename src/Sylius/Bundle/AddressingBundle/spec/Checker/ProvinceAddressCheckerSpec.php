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

namespace spec\Sylius\Bundle\AddressingBundle\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\AddressingBundle\Checker\ProvinceAddressCheckerInterface;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class ProvinceAddressCheckerSpec extends ObjectBehavior
{
    function let(RepositoryInterface $countryRepository, RepositoryInterface $provinceRepository): void
    {
        $this->beConstructedWith($countryRepository, $provinceRepository);
    }

    function it_is_province_address_checker(): void
    {
        $this->shouldImplement(ProvinceAddressCheckerInterface::class);
    }

    function it_confirms_that_province_is_valid_if_related_country_does_not_exists(
        RepositoryInterface $countryRepository,
        AddressInterface $address
    ): void {
        $address->getCountryCode()->willReturn('USD');
        $countryRepository->findOneBy(['code' => 'USD'])->willReturn(null);

        $this->isValid($address)->shouldReturn(true);
    }

    function it_does_not_confirm_that_province_is_valid_if_related_country_does_exists_but_it_does_not_have_provinces(
        RepositoryInterface $countryRepository,
        CountryInterface $country,
        AddressInterface $address
    ): void {
        $address->getCountryCode()->willReturn('USD');
        $address->getProvinceCode()->willReturn(null);
        $country->hasProvinces()->willReturn(false);

        $countryRepository->findOneBy(['code' => 'USD'])->willReturn($country);

        $this->isValid($address)->shouldReturn(true);
    }

    function it_does_not_confirm_that_province_is_valid_if_related_country_does_exists_it_does_not_have_provinces_but_province_code_is_provided(
        RepositoryInterface $countryRepository,
        CountryInterface $country,
        AddressInterface $address
    ): void {
        $address->getCountryCode()->willReturn('USD');
        $address->getProvinceCode()->willReturn('US-FL');

        $country->hasProvinces()->willReturn(false);

        $countryRepository->findOneBy(['code' => 'USD'])->willReturn($country);

        $this->isValid($address)->shouldReturn(false);
    }

    function it_does_not_confirm_that_province_is_valid_if_related_country_does_exists_it_has_provinces_but_province_code_is_not_provided(
        RepositoryInterface $countryRepository,
        CountryInterface $country,
        AddressInterface $address
    ): void {
        $address->getCountryCode()->willReturn('USD');
        $address->getProvinceCode()->willReturn(null);

        $country->hasProvinces()->willReturn(true);

        $countryRepository->findOneBy(['code' => 'USD'])->willReturn($country);

        $this->isValid($address)->shouldReturn(false);
    }

    function it_does_not_confirm_that_province_is_valid_if_related_country_does_exists_it_has_provinces_province_code_is_provided_but_province_does_not_exists(
        RepositoryInterface $countryRepository,
        RepositoryInterface $provinceRepository,
        CountryInterface $country,
        AddressInterface $address
    ): void {
        $address->getCountryCode()->willReturn('USD');
        $address->getProvinceCode()->willReturn('US-FL');

        $country->hasProvinces()->willReturn(true);

        $countryRepository->findOneBy(['code' => 'USD'])->willReturn($country);
        $provinceRepository->findOneBy(['code' => 'US-FL'])->willReturn(null);

        $this->isValid($address)->shouldReturn(false);
    }

    function it_does_not_confirm_that_province_is_valid_if_related_country_does_exists_it_has_provinces_province_code_is_provided_province_does_exists_but_is_not_part_of_country(
        RepositoryInterface $countryRepository,
        RepositoryInterface $provinceRepository,
        CountryInterface $country,
        ProvinceInterface $province,
        AddressInterface $address
    ): void {
        $address->getCountryCode()->willReturn('USD');
        $address->getProvinceCode()->willReturn('US-FL');

        $country->hasProvinces()->willReturn(true);
        $country->hasProvince($province)->willReturn(false);

        $countryRepository->findOneBy(['code' => 'USD'])->willReturn($country);
        $provinceRepository->findOneBy(['code' => 'US-FL'])->willReturn($province);

        $this->isValid($address)->shouldReturn(false);
    }

    function it_confirms_that_province_is_valid_if_related_country_does_exists_it_has_provinces_province_code_is_provided_province_does_exists_and_it_is_part_of_country(
        RepositoryInterface $countryRepository,
        RepositoryInterface $provinceRepository,
        CountryInterface $country,
        ProvinceInterface $province,
        AddressInterface $address
    ): void {
        $address->getCountryCode()->willReturn('USD');
        $address->getProvinceCode()->willReturn('US-FL');

        $country->hasProvinces()->willReturn(true);
        $country->hasProvince($province)->willReturn(true);

        $countryRepository->findOneBy(['code' => 'USD'])->willReturn($country);
        $provinceRepository->findOneBy(['code' => 'US-FL'])->willReturn($province);

        $this->isValid($address)->shouldReturn(true);
    }
}
