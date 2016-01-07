<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Addressing\Matcher;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberCountry;
use Sylius\Component\Addressing\Model\ZoneMemberProvince;
use Sylius\Component\Addressing\Model\ZoneMemberZone;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class ZoneMatcherSpec extends ObjectBehavior
{
    function let(RepositoryInterface $repository)
    {
        $this->beConstructedWith($repository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Addressing\Matcher\ZoneMatcher');
    }

    function it_is_Sylius_zone_matcher()
    {
        $this->shouldImplement(ZoneMatcherInterface::class);
    }

    function it_returns_null_if_there_are_no_zones($repository, AddressInterface $address)
    {
        $repository->findAll()->willReturn(array());
        $this->match($address)->shouldReturn(null);
    }

    function it_should_match_address_by_province(
        $repository,
        ProvinceInterface $province,
        AddressInterface $address,
        ZoneMemberProvince $memberProvince,
        ZoneInterface $zone
    ) {
        $repository->findAll()->shouldBeCalled()->willReturn(array($zone));
        $address->getProvince()->shouldBeCalled()->willReturn($province);
        $memberProvince->getProvince()->shouldBeCalled()->willReturn($province);
        $zone->getType()->shouldBeCalled()->willReturn(ZoneInterface::TYPE_PROVINCE);
        $zone->getMembers()->shouldBeCalled()->willReturn(array($memberProvince));
        $memberProvince->getBelongsTo()->willReturn($zone);

        $this->match($address)->shouldReturn($zone);
    }

    function it_should_match_address_by_province_and_scope(
        $repository,
        ProvinceInterface $province,
        AddressInterface $address,
        ZoneMemberProvince $memberProvince,
        ZoneInterface $zone
    ) {
        $repository->findBy(array('scope' => 'shipping'))->shouldBeCalled()->willReturn(array($zone));
        $address->getProvince()->shouldBeCalled()->willReturn($province);
        $memberProvince->getProvince()->shouldBeCalled()->willReturn($province);
        $zone->getType()->shouldBeCalled()->willReturn(ZoneInterface::TYPE_PROVINCE);
        $zone->getMembers()->shouldBeCalled()->willReturn(array($memberProvince));
        $memberProvince->getBelongsTo()->willReturn($zone);

        $this->match($address, 'shipping')->shouldReturn($zone);
    }

    function it_should_match_address_by_country(
        RepositoryInterface$repository,
        CountryInterface $country,
        AddressInterface $address,
        ZoneMemberCountry $memberCountry,
        ZoneInterface $zone
    ) {
        $repository->findAll()->shouldBeCalled()->willReturn(array($zone));
        $address->getCountry()->shouldBeCalled()->willReturn($country);
        $memberCountry->getCountry()->shouldBeCalled()->willReturn($country);
        $zone->getType()->shouldBeCalled()->willReturn(ZoneInterface::TYPE_COUNTRY);
        $zone->getMembers()->shouldBeCalled()->willReturn(array($memberCountry));
        $memberCountry->getBelongsTo()->willReturn($zone);

        $this->match($address)->shouldReturn($zone);
    }

    function it_should_match_address_by_country_and_scope(
        $repository,
        CountryInterface $country,
        AddressInterface $address,
        ZoneMemberCountry $memberCountry,
        ZoneInterface $zone
    ) {
        $repository->findBy(array('scope' => 'shipping'))->shouldBeCalled()->willReturn(array($zone));
        $address->getCountry()->shouldBeCalled()->willReturn($country);
        $memberCountry->getCountry()->shouldBeCalled()->willReturn($country);
        $zone->getType()->shouldBeCalled()->willReturn(ZoneInterface::TYPE_COUNTRY);
        $zone->getMembers()->shouldBeCalled()->willReturn(array($memberCountry));
        $memberCountry->getBelongsTo()->willReturn($zone);

        $this->match($address, 'shipping')->shouldReturn($zone);
    }

    function it_should_match_address_for_nested_zones(
        $repository,
        CountryInterface $country,
        AddressInterface $address,
        ZoneMemberCountry $memberCountry,
        ZoneInterface $subZone,
        ZoneMemberZone $memberZone,
        ZoneInterface $rootZone
    ) {
        $address->getCountry()->shouldBeCalled()->willReturn($country);
        $memberCountry->getCountry()->shouldBeCalled()->willReturn($country);
        $subZone->getMembers()->shouldBeCalled()->willReturn(array($memberCountry));
        $subZone->getType()->shouldBeCalled()->willReturn(ZoneInterface::TYPE_COUNTRY);
        $memberZone->getZone()->shouldBeCalled()->willReturn($subZone);
        $rootZone->getMembers()->shouldBeCalled()->willReturn(array($memberZone));
        $rootZone->getType()->shouldBeCalled()->willReturn(ZoneInterface::TYPE_ZONE);

        $memberCountry->getBelongsTo()->willReturn($subZone);
        $memberZone->getBelongsTo()->willReturn($rootZone);
        $repository->findAll()->shouldBeCalled()->willReturn(array($rootZone));

        $this->match($address)->shouldReturn($rootZone);
    }

    function it_should_match_address_for_nested_zones_and_scope(
        $repository,
        CountryInterface $country,
        AddressInterface $address,
        ZoneMemberCountry $memberCountry,
        ZoneInterface $subZone,
        ZoneMemberZone $memberZone,
        ZoneInterface $rootZone
    ) {
        $address->getCountry()->shouldBeCalled()->willReturn($country);
        $memberCountry->getCountry()->shouldBeCalled()->willReturn($country);
        $subZone->getMembers()->shouldBeCalled()->willReturn(array($memberCountry));
        $subZone->getType()->shouldBeCalled()->willReturn(ZoneInterface::TYPE_COUNTRY);
        $memberZone->getZone()->shouldBeCalled()->willReturn($subZone);
        $rootZone->getMembers()->shouldBeCalled()->willReturn(array($memberZone));
        $rootZone->getType()->shouldBeCalled()->willReturn(ZoneInterface::TYPE_ZONE);

        $memberCountry->getBelongsTo()->willReturn($subZone);
        $memberZone->getBelongsTo()->willReturn($rootZone);
        $repository->findBy(array('scope' => 'shipping'))->shouldBeCalled()->willReturn(array($rootZone));

        $this->match($address, 'shipping')->shouldReturn($rootZone);
    }

    function it_should_match_address_from_province_when_many_are_found(
        $repository,
        CountryInterface $country,
        ProvinceInterface $province,
        AddressInterface $address,
        ZoneMemberCountry $memberCountry,
        ZoneMemberProvince $memberProvince,
        ZoneInterface $zoneCountry,
        ZoneInterface $zoneProvince
    ) {
        $address->getProvince()->willReturn($province);
        $address->getCountry()->willReturn($country);
        $memberProvince->getProvince()->willReturn($province);
        $memberCountry->getCountry()->willReturn($country);

        $zoneProvince->getMembers()->willReturn(array($memberProvince));
        $zoneProvince->getType()->willReturn(ZoneInterface::TYPE_PROVINCE);
        $zoneCountry->getMembers()->willReturn(array($memberCountry));
        $zoneCountry->getType()->willReturn(ZoneInterface::TYPE_COUNTRY);

        $repository->findAll()->shouldBeCalled()->willReturn(array($zoneCountry, $zoneProvince));
        $memberProvince->getBelongsTo()->willReturn($zoneProvince);
        $memberCountry->getBelongsTo()->willReturn($zoneCountry);

        $this->match($address)->shouldReturn($zoneProvince);
    }

    function it_should_match_address_from_province_when_many_are_found_by_scope(
        $repository,
        CountryInterface $country,
        ProvinceInterface $province,
        AddressInterface $address,
        ZoneMemberCountry $memberCountry,
        ZoneMemberProvince $memberProvince,
        ZoneInterface $zoneCountry,
        ZoneInterface $zoneProvince
    ) {
        $address->getProvince()->willReturn($province);
        $address->getCountry()->willReturn($country);
        $memberProvince->getProvince()->willReturn($province);
        $memberCountry->getCountry()->willReturn($country);

        $zoneProvince->getMembers()->willReturn(array($memberProvince));
        $zoneProvince->getType()->willReturn(ZoneInterface::TYPE_PROVINCE);
        $zoneCountry->getMembers()->willReturn(array($memberCountry));
        $zoneCountry->getType()->willReturn(ZoneInterface::TYPE_COUNTRY);

        $repository->findBy(array('scope' => 'shipping'))->shouldBeCalled()->willReturn(array($zoneCountry, $zoneProvince));
        $memberProvince->getBelongsTo()->willReturn($zoneProvince);
        $memberCountry->getBelongsTo()->willReturn($zoneCountry);

        $this->match($address, 'shipping')->shouldReturn($zoneProvince);
    }

    function it_should_match_all_zones_when_one_zone_for_address_is_defined(
        $repository,
        CountryInterface $country,
        AddressInterface $address,
        ZoneMemberCountry $memberCountry,
        ZoneInterface $zoneCountry
    ) {
        $repository->findAll()->shouldBeCalled()->willReturn(array($zoneCountry));
        $address->getCountry()->shouldBeCalled()->willReturn($country);
        $memberCountry->getCountry()->shouldBeCalled()->willReturn($country);
        $zoneCountry->getType()->shouldBeCalled()->willReturn(ZoneInterface::TYPE_COUNTRY);
        $zoneCountry->getMembers()->shouldBeCalled()->willReturn(array($memberCountry));
        $memberCountry->getBelongsTo()->willReturn($zoneCountry);

        $this->matchAll($address)->shouldReturn(array($zoneCountry));
    }

    function it_should_match_all_zones_by_scope_when_one_zone_for_address_is_defined(
        $repository,
        CountryInterface $country,
        AddressInterface $address,
        ZoneMemberCountry $memberCountry,
        ZoneInterface $zoneCountry
    ) {
        $repository->findBy(array('scope' => 'shipping'))->shouldBeCalled()->willReturn(array($zoneCountry));
        $address->getCountry()->shouldBeCalled()->willReturn($country);
        $memberCountry->getCountry()->shouldBeCalled()->willReturn($country);
        $zoneCountry->getType()->shouldBeCalled()->willReturn(ZoneInterface::TYPE_COUNTRY);
        $zoneCountry->getMembers()->shouldBeCalled()->willReturn(array($memberCountry));
        $memberCountry->getBelongsTo()->willReturn($zoneCountry);

        $this->matchAll($address, 'shipping')->shouldReturn(array($zoneCountry));
    }
}
