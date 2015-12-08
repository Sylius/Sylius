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
        RepositoryInterface $repository,
        ProvinceInterface $province,
        AddressInterface $address,
        ZoneMemberProvince $memberProvince,
        ZoneInterface $zone
    ) {
        $province->getCode()->willReturn('DU');
        $repository->findAll()->willReturn(array($zone));
        $address->getProvince()->willReturn('DU');
        $memberProvince->getProvince()->willReturn($province);

        $zone->getType()->willReturn(ZoneInterface::TYPE_PROVINCE);
        $zone->getMembers()->willReturn(array($memberProvince));
        $memberProvince->getBelongsTo()->willReturn($zone);

        $this->match($address)->shouldReturn($zone);
    }

    function it_should_match_address_by_province_and_scope(
        RepositoryInterface $repository,
        ProvinceInterface $province,
        AddressInterface $address,
        ZoneMemberProvince $memberProvince,
        ZoneInterface $zone
    ) {
        $repository->findBy(array('scope' => 'shipping'))->shouldBeCalled()->willReturn(array($zone));
        $province->getCode()->willReturn('TX');
        $address->getProvince()->willReturn('TX');
        $memberProvince->getProvince()->willReturn($province);
        $zone->getType()->willReturn(ZoneInterface::TYPE_PROVINCE);
        $zone->getMembers()->willReturn(array($memberProvince));
        $memberProvince->getBelongsTo()->willReturn($zone);

        $this->match($address, 'shipping')->shouldReturn($zone);
    }

    function it_should_match_address_by_country(
        RepositoryInterface $repository,
        CountryInterface $country,
        AddressInterface $address,
        ZoneMemberCountry $memberCountry,
        ZoneInterface $zone
    ) {
        $repository->findAll()->willReturn(array($zone));
        $country->getCode()->willReturn('IE');
        $address->getCountry()->willReturn('IE');
        $memberCountry->getCountry()->willReturn($country);
        $zone->getType()->willReturn(ZoneInterface::TYPE_COUNTRY);
        $zone->getMembers()->willReturn(array($memberCountry));
        $memberCountry->getBelongsTo()->willReturn($zone);

        $this->match($address)->shouldReturn($zone);
    }

    function it_should_match_address_by_country_and_scope(
        RepositoryInterface $repository,
        CountryInterface $country,
        AddressInterface $address,
        ZoneMemberCountry $memberCountry,
        ZoneInterface $zone
    ) {
        $repository->findBy(array('scope' => 'shipping'))->willReturn(array($zone));
        $country->getCode()->willReturn('IE');
        $address->getCountry()->willReturn('IE');
        $memberCountry->getCountry()->willReturn($country);
        $zone->getType()->willReturn(ZoneInterface::TYPE_COUNTRY);
        $zone->getMembers()->willReturn(array($memberCountry));
        $memberCountry->getBelongsTo()->willReturn($zone);

        $this->match($address, 'shipping')->shouldReturn($zone);
    }

    function it_should_match_address_for_nested_zones(
        RepositoryInterface $repository,
        CountryInterface $country,
        AddressInterface $address,
        ZoneMemberCountry $memberCountry,
        ZoneInterface $subZone,
        ZoneMemberZone $memberZone,
        ZoneInterface $rootZone
    ) {
        $country->getCode()->willReturn('IE');

        $address->getCountry()->willReturn('IE');
        $memberCountry->getCountry()->willReturn($country);
        $subZone->getMembers()->willReturn(array($memberCountry));
        $subZone->getType()->willReturn(ZoneInterface::TYPE_COUNTRY);
        $memberZone->getZone()->willReturn($subZone);
        $rootZone->getMembers()->willReturn(array($memberZone));
        $rootZone->getType()->willReturn(ZoneInterface::TYPE_ZONE);

        $memberCountry->getBelongsTo()->willReturn($subZone);
        $memberZone->getBelongsTo()->willReturn($rootZone);
        $repository->findAll()->willReturn(array($rootZone));

        $this->match($address)->shouldReturn($rootZone);
    }

    function it_should_match_address_for_nested_zones_and_scope(
        RepositoryInterface $repository,
        CountryInterface $country,
        AddressInterface $address,
        ZoneMemberCountry $memberCountry,
        ZoneInterface $subZone,
        ZoneMemberZone $memberZone,
        ZoneInterface $rootZone
    ) {
        $country->getCode()->willReturn('IE');
        $address->getCountry()->willReturn('IE');

        $memberCountry->getCountry()->willReturn($country);
        $subZone->getMembers()->willReturn(array($memberCountry));
        $subZone->getType()->willReturn(ZoneInterface::TYPE_COUNTRY);
        $memberZone->getZone()->willReturn($subZone);

        $rootZone->getMembers()->willReturn(array($memberZone));
        $rootZone->getType()->willReturn(ZoneInterface::TYPE_ZONE);

        $memberCountry->getBelongsTo()->willReturn($subZone);
        $memberZone->getBelongsTo()->willReturn($rootZone);
        $repository->findBy(array('scope' => 'shipping'))->willReturn(array($rootZone));

        $this->match($address, 'shipping')->shouldReturn($rootZone);
    }

    function it_matches_address_from_province_when_many_are_found(
        RepositoryInterface $repository,
        CountryInterface $country,
        ProvinceInterface $province,
        AddressInterface $address,
        ZoneMemberCountry $memberCountry,
        ZoneMemberProvince $memberProvince,
        ZoneInterface $zoneCountry,
        ZoneInterface $zoneProvince
    ) {
        $province->getCode()->willReturn('DU');
        $country->getCode()->willReturn('IE');

        $address->getCountry()->willReturn('IE');
        $address->getProvince()->willReturn('DU');

        $memberCountry->getCountry()->willReturn($country);
        $memberProvince->getProvince()->willReturn($province);

        $zoneProvince->getMembers()->willReturn(array($memberProvince));
        $zoneProvince->getType()->willReturn(ZoneInterface::TYPE_PROVINCE);
        $zoneCountry->getMembers()->willReturn(array($memberCountry));
        $zoneCountry->getType()->willReturn(ZoneInterface::TYPE_COUNTRY);

        $repository->findAll()->willReturn(array($zoneCountry, $zoneProvince));
        $memberProvince->getBelongsTo()->willReturn($zoneProvince);
        $memberCountry->getBelongsTo()->willReturn($zoneCountry);

        $this->match($address)->shouldReturn($zoneProvince);
    }

    function it_matches_address_from_province_when_many_are_found_by_scope(
        RepositoryInterface $repository,
        CountryInterface $country,
        ProvinceInterface $province,
        AddressInterface $address,
        ZoneMemberCountry $memberCountry,
        ZoneMemberProvince $memberProvince,
        ZoneInterface $zoneCountry,
        ZoneInterface $zoneProvince
    ) {
        $province->getCode()->willReturn('DU');
        $country->getCode()->willReturn('IE');

        $address->getCountry()->willReturn('IE');
        $address->getProvince()->willReturn('DU');
        $memberProvince->getProvince()->willReturn($province);
        $memberCountry->getCountry()->willReturn($country);

        $zoneProvince->getMembers()->willReturn(array($memberProvince));
        $zoneProvince->getType()->willReturn(ZoneInterface::TYPE_PROVINCE);
        $zoneCountry->getMembers()->willReturn(array($memberCountry));
        $zoneCountry->getType()->willReturn(ZoneInterface::TYPE_COUNTRY);

        $repository->findBy(array('scope' => 'shipping'))->willReturn(array($zoneCountry, $zoneProvince));
        $memberProvince->getBelongsTo()->willReturn($zoneProvince);
        $memberCountry->getBelongsTo()->willReturn($zoneCountry);

        $this->match($address, 'shipping')->shouldReturn($zoneProvince);
    }

    function it_matches_all_zones_with_given_address(
        RepositoryInterface $repository,
        CountryInterface $country,
        AddressInterface $address,
        ZoneMemberCountry $memberCountry,
        ZoneMemberZone $memberZone,
        ZoneInterface $zoneCountry,
        ZoneInterface $zoneZone
    ) {
        $repository->findAll()->willReturn(array($zoneCountry, $zoneZone));

        $country->getCode()->willReturn('US');
        $address->getCountry()->willReturn('US');

        $memberCountry->getCountry()->willReturn($country);
        $zoneCountry->getType()->willReturn(ZoneInterface::TYPE_COUNTRY);
        $zoneCountry->getMembers()->willReturn(array($memberCountry));
        $memberCountry->getBelongsTo()->willReturn($zoneCountry);

        $memberZone->getZone()->willReturn($zoneCountry);
        $zoneZone->getType()->willReturn(ZoneInterface::TYPE_ZONE);
        $zoneZone->getMembers()->willReturn(array($memberZone));
        $memberZone->getBelongsTo()->willReturn($zoneZone);

        $this->matchAll($address)->shouldReturn(array($zoneCountry, $zoneZone));
    }

    function it_matches_all_zones_by_scope_when_one_zone_for_address_is_defined(
        RepositoryInterface $repository,
        CountryInterface $country,
        AddressInterface $address,
        ZoneMemberCountry $memberCountry,
        ZoneInterface $zoneCountry
    ) {
        $repository->findBy(array('scope' => 'shipping'))->willReturn(array($zoneCountry));

        $country->getCode()->willReturn('US');
        $address->getCountry()->willReturn('US');

        $memberCountry->getCountry()->willReturn($country);
        $zoneCountry->getType()->willReturn(ZoneInterface::TYPE_COUNTRY);
        $zoneCountry->getMembers()->willReturn(array($memberCountry));
        $memberCountry->getBelongsTo()->willReturn($zoneCountry);

        $this->matchAll($address, 'shipping')->shouldReturn(array($zoneCountry));
    }
}
