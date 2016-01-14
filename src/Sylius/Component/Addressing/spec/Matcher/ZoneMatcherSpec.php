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
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @author Jan Góralski <jan.goralski@lakion.com>
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

    function it_implements_zone_matcher_interface()
    {
        $this->shouldImplement(ZoneMatcherInterface::class);
    }

    function it_returns_null_if_there_are_no_zones(RepositoryInterface $repository, AddressInterface $address)
    {
        $repository->findAll()->willReturn(array());
        $this->match($address)->shouldReturn(null);
    }

    function it_should_match_address_by_province(
        RepositoryInterface $repository,
        ProvinceInterface $province,
        AddressInterface $address,
        ZoneMemberInterface $memberProvince,
        ZoneInterface $zone
    ) {
        $province->getCode()->willReturn('DU');
        $repository->findAll()->willReturn(array($zone));
        $address->getProvince()->willReturn('DU');
        $memberProvince->getCode()->willReturn('DU');

        $zone->getType()->willReturn(ZoneInterface::TYPE_PROVINCE);
        $zone->getMembers()->willReturn(array($memberProvince));
        $memberProvince->getBelongsTo()->willReturn($zone);

        $this->match($address)->shouldReturn($zone);
    }

    function it_should_match_address_by_province_and_scope(
        RepositoryInterface $repository,
        ProvinceInterface $province,
        AddressInterface $address,
        ZoneMemberInterface $memberProvince,
        ZoneInterface $zone
    ) {
        $repository->findBy(array('scope' => 'shipping'))->shouldBeCalled()->willReturn(array($zone));
        $province->getCode()->willReturn('TX');
        $address->getProvince()->willReturn('TX');
        $memberProvince->getCode()->willReturn('TX');
        $zone->getType()->willReturn(ZoneInterface::TYPE_PROVINCE);
        $zone->getMembers()->willReturn(array($memberProvince));
        $memberProvince->getBelongsTo()->willReturn($zone);

        $this->match($address, 'shipping')->shouldReturn($zone);
    }

    function it_should_match_address_by_country(
        RepositoryInterface $repository,
        CountryInterface $country,
        AddressInterface $address,
        ZoneMemberInterface $memberCountry,
        ZoneInterface $zone
    ) {
        $repository->findAll()->willReturn(array($zone));
        $country->getCode()->willReturn('IE');
        $address->getCountry()->willReturn('IE');
        $memberCountry->getCode()->willReturn('IE');
        $zone->getType()->willReturn(ZoneInterface::TYPE_COUNTRY);
        $zone->getMembers()->willReturn(array($memberCountry));
        $memberCountry->getBelongsTo()->willReturn($zone);

        $this->match($address)->shouldReturn($zone);
    }

    function it_should_match_address_by_country_and_scope(
        RepositoryInterface $repository,
        CountryInterface $country,
        AddressInterface $address,
        ZoneMemberInterface $memberCountry,
        ZoneInterface $zone
    ) {
        $repository->findBy(array('scope' => 'shipping'))->willReturn(array($zone));
        $country->getCode()->willReturn('IE');
        $address->getCountry()->willReturn('IE');
        $memberCountry->getCode()->willReturn('IE');
        $zone->getType()->willReturn(ZoneInterface::TYPE_COUNTRY);
        $zone->getMembers()->willReturn(array($memberCountry));
        $memberCountry->getBelongsTo()->willReturn($zone);

        $this->match($address, 'shipping')->shouldReturn($zone);
    }

    function it_should_match_address_for_nested_zones(
        RepositoryInterface $repository,
        CountryInterface $country,
        AddressInterface $address,
        ZoneMemberInterface $memberCountry,
        ZoneMemberInterface $memberZone,
        ZoneInterface $subZone,
        ZoneInterface $rootZone
    ) {
        $country->getCode()->willReturn('IE');

        $address->getCountry()->willReturn('IE');
        $memberCountry->getCode()->willReturn('IE');
        $subZone->getMembers()->willReturn(array($memberCountry));
        $subZone->getType()->willReturn(ZoneInterface::TYPE_COUNTRY);
        $subZone->getCode()->willReturn('Ireland');
        $memberZone->getCode()->willReturn('Ireland');
        $rootZone->getMembers()->willReturn(array($memberZone));
        $rootZone->getType()->willReturn(ZoneInterface::TYPE_ZONE);

        $memberCountry->getBelongsTo()->willReturn($subZone);
        $memberZone->getBelongsTo()->willReturn($rootZone);
        $repository->findOneBy(array('code' => 'Ireland'))->willReturn($subZone);
        $repository->findAll()->willReturn(array($rootZone));

        $this->match($address)->shouldReturn($rootZone);
    }

    function it_should_match_address_for_nested_zones_and_scope(
        RepositoryInterface $repository,
        CountryInterface $country,
        AddressInterface $address,
        ZoneMemberInterface $memberCountry,
        ZoneMemberInterface $memberZone,
        ZoneInterface $subZone,
        ZoneInterface $rootZone
    ) {
        $country->getCode()->willReturn('IE');
        $address->getCountry()->willReturn('IE');

        $memberCountry->getCode()->willReturn('IE');
        $subZone->getMembers()->willReturn(array($memberCountry));
        $subZone->getType()->willReturn(ZoneInterface::TYPE_COUNTRY);
        $subZone->getCode()->willReturn('Ireland');
        $memberZone->getCode()->willReturn('Ireland');

        $rootZone->getMembers()->willReturn(array($memberZone));
        $rootZone->getType()->willReturn(ZoneInterface::TYPE_ZONE);

        $memberCountry->getBelongsTo()->willReturn($subZone);
        $memberZone->getBelongsTo()->willReturn($rootZone);
        $repository->findOneBy(array('code' => 'Ireland'))->willReturn($subZone);
        $repository->findBy(array('scope' => 'shipping'))->willReturn(array($rootZone));

        $this->match($address, 'shipping')->shouldReturn($rootZone);
    }

    function it_matches_address_from_province_when_many_are_found(
        RepositoryInterface $repository,
        CountryInterface $country,
        ProvinceInterface $province,
        AddressInterface $address,
        ZoneMemberInterface $memberCountry,
        ZoneMemberInterface $memberProvince,
        ZoneInterface $zoneCountry,
        ZoneInterface $zoneProvince
    ) {
        $province->getCode()->willReturn('DU');
        $country->getCode()->willReturn('IE');

        $address->getCountry()->willReturn('IE');
        $address->getProvince()->willReturn('DU');

        $memberCountry->getCode()->willReturn('IE');
        $memberProvince->getCode()->willReturn('DU');

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
        AddressInterface $address,
        ZoneMemberInterface $memberCountry,
        ZoneMemberInterface $memberProvince,
        ZoneInterface $zoneCountry,
        ZoneInterface $zoneProvince
    ) {
        $address->getCountry()->willReturn('IE');
        $memberCountry->getCode()->willReturn('IE');

        $address->getProvince()->willReturn('DU');
        $memberProvince->getCode()->willReturn('DU');

        $zoneCountry->getMembers()->willReturn(array($memberCountry));
        $zoneCountry->getType()->willReturn(ZoneInterface::TYPE_COUNTRY);

        $zoneProvince->getMembers()->willReturn(array($memberProvince));
        $zoneProvince->getType()->willReturn(ZoneInterface::TYPE_PROVINCE);

        $repository->findBy(array('scope' => 'shipping'))->willReturn(array($zoneCountry, $zoneProvince));
        $memberProvince->getBelongsTo()->willReturn($zoneProvince);
        $memberCountry->getBelongsTo()->willReturn($zoneCountry);

        $this->match($address, 'shipping')->shouldReturn($zoneProvince);
    }

    function it_matches_all_zones_with_given_address(
        RepositoryInterface $repository,
        AddressInterface $address,
        ZoneMemberInterface $memberProvince,
        ZoneMemberInterface $memberCountry,
        ZoneMemberInterface $memberZone,
        ZoneInterface $zoneProvince,
        ZoneInterface $zoneCountry,
        ZoneInterface $zoneZone
    ) {
        $repository->findAll()->willReturn(array($zoneProvince, $zoneCountry, $zoneZone));

        $address->getProvince()->willReturn('TX');
        $memberProvince->getCode()->willReturn('TX');

        $memberProvince->getBelongsTo()->willReturn($zoneProvince);
        $zoneProvince->getType()->willReturn(ZoneInterface::TYPE_PROVINCE);
        $zoneProvince->getMembers()->willReturn(array($memberProvince));

        $address->getCountry()->willReturn('US');
        $memberCountry->getCode()->willReturn('US');

        $zoneCountry->getType()->willReturn(ZoneInterface::TYPE_COUNTRY);
        $zoneCountry->getMembers()->willReturn(array($memberCountry));
        $zoneCountry->getCode()->willReturn('USA');
        $memberCountry->getBelongsTo()->willReturn($zoneCountry);

        $memberZone->getCode()->willReturn('USA');
        $zoneZone->getType()->willReturn(ZoneInterface::TYPE_ZONE);
        $zoneZone->getMembers()->willReturn(array($memberZone));
        $memberZone->getBelongsTo()->willReturn($zoneZone);

        $repository->findOneBy(array('code' => 'USA'))->willReturn($zoneCountry);

        $this->matchAll($address)->shouldReturn(array($zoneProvince, $zoneCountry, $zoneZone));
    }

    function it_matches_all_zones_by_scope_when_one_zone_for_address_is_defined(
        RepositoryInterface $repository,
        AddressInterface $address,
        ZoneMemberInterface $memberCountry,
        ZoneInterface $zoneCountry
    ) {
        $repository->findBy(array('scope' => 'shipping'))->willReturn(array($zoneCountry));

        $address->getCountry()->willReturn('US');

        $memberCountry->getCode()->willReturn('US');
        $zoneCountry->getType()->willReturn(ZoneInterface::TYPE_COUNTRY);
        $zoneCountry->getMembers()->willReturn(array($memberCountry));
        $memberCountry->getBelongsTo()->willReturn($zoneCountry);

        $this->matchAll($address, 'shipping')->shouldReturn(array($zoneCountry));
    }
}
