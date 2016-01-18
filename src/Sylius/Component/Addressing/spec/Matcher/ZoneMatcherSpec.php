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
use Sylius\Component\Addressing\Model\AdministrativeAreaInterface;
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

    function it_matches_address_by_administrative_area(
        RepositoryInterface $repository,
        AdministrativeAreaInterface $administrativeArea,
        AddressInterface $address,
        ZoneMemberInterface $memberAdministrativeArea,
        ZoneInterface $zone
    ) {
        $administrativeArea->getCode()->willReturn('DU');
        $repository->findAll()->willReturn(array($zone));
        $address->getAdministrativeArea()->willReturn('DU');
        $memberAdministrativeArea->getCode()->willReturn('DU');

        $zone->getType()->willReturn(ZoneInterface::TYPE_ADMINISTRATIVE_AREA);
        $zone->getMembers()->willReturn(array($memberAdministrativeArea));
        $memberAdministrativeArea->getBelongsTo()->willReturn($zone);

        $this->match($address)->shouldReturn($zone);
    }

    function it_matches_address_by_administrative_area_and_scope(
        RepositoryInterface $repository,
        AdministrativeAreaInterface $administrativeArea,
        AddressInterface $address,
        ZoneMemberInterface $memberAdministrativeArea,
        ZoneInterface $zone
    ) {
        $repository->findBy(array('scope' => 'shipping'))->willReturn(array($zone));
        $administrativeArea->getCode()->willReturn('TX');
        $address->getAdministrativeArea()->willReturn('TX');
        $memberAdministrativeArea->getCode()->willReturn('TX');
        $zone->getType()->willReturn(ZoneInterface::TYPE_ADMINISTRATIVE_AREA);
        $zone->getMembers()->willReturn(array($memberAdministrativeArea));
        $memberAdministrativeArea->getBelongsTo()->willReturn($zone);

        $this->match($address, 'shipping')->shouldReturn($zone);
    }

    function it_matches_address_by_country(
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

    function it_matches_address_by_country_and_scope(
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

    function it_matches_address_for_nested_zones(
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

    function it_matches_address_for_nested_zones_and_scope(
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

    function it_matches_address_from_administrative_area_when_many_are_found(
        RepositoryInterface $repository,
        CountryInterface $country,
        AdministrativeAreaInterface $administrativeArea,
        AddressInterface $address,
        ZoneMemberInterface $memberCountry,
        ZoneMemberInterface $memberAdministrativeArea,
        ZoneInterface $zoneCountry,
        ZoneInterface $zoneAdministrativeArea
    ) {
        $administrativeArea->getCode()->willReturn('DU');
        $country->getCode()->willReturn('IE');

        $address->getCountry()->willReturn('IE');
        $address->getAdministrativeArea()->willReturn('DU');

        $memberCountry->getCode()->willReturn('IE');
        $memberAdministrativeArea->getCode()->willReturn('DU');

        $zoneAdministrativeArea->getMembers()->willReturn(array($memberAdministrativeArea));
        $zoneAdministrativeArea->getType()->willReturn(ZoneInterface::TYPE_ADMINISTRATIVE_AREA);
        $zoneCountry->getMembers()->willReturn(array($memberCountry));
        $zoneCountry->getType()->willReturn(ZoneInterface::TYPE_COUNTRY);

        $repository->findAll()->willReturn(array($zoneCountry, $zoneAdministrativeArea));
        $memberAdministrativeArea->getBelongsTo()->willReturn($zoneAdministrativeArea);
        $memberCountry->getBelongsTo()->willReturn($zoneCountry);

        $this->match($address)->shouldReturn($zoneAdministrativeArea);
    }

    function it_matches_address_from_administrative_area_when_many_are_found_by_scope(
        RepositoryInterface $repository,
        AddressInterface $address,
        ZoneMemberInterface $memberCountry,
        ZoneMemberInterface $memberAdministrativeArea,
        ZoneInterface $zoneCountry,
        ZoneInterface $zoneAdministrativeArea
    ) {
        $address->getCountry()->willReturn('IE');
        $memberCountry->getCode()->willReturn('IE');

        $address->getAdministrativeArea()->willReturn('DU');
        $memberAdministrativeArea->getCode()->willReturn('DU');

        $zoneCountry->getMembers()->willReturn(array($memberCountry));
        $zoneCountry->getType()->willReturn(ZoneInterface::TYPE_COUNTRY);

        $zoneAdministrativeArea->getMembers()->willReturn(array($memberAdministrativeArea));
        $zoneAdministrativeArea->getType()->willReturn(ZoneInterface::TYPE_ADMINISTRATIVE_AREA);

        $repository->findBy(array('scope' => 'shipping'))->willReturn(array($zoneCountry, $zoneAdministrativeArea));
        $memberAdministrativeArea->getBelongsTo()->willReturn($zoneAdministrativeArea);
        $memberCountry->getBelongsTo()->willReturn($zoneCountry);

        $this->match($address, 'shipping')->shouldReturn($zoneAdministrativeArea);
    }

    function it_matches_all_zones_with_given_address(
        RepositoryInterface $repository,
        AddressInterface $address,
        ZoneMemberInterface $memberAdministrativeArea,
        ZoneMemberInterface $memberCountry,
        ZoneMemberInterface $memberZone,
        ZoneInterface $zoneAdministrativeArea,
        ZoneInterface $zoneCountry,
        ZoneInterface $zoneZone
    ) {
        $repository->findAll()->willReturn(array($zoneAdministrativeArea, $zoneCountry, $zoneZone));

        $address->getAdministrativeArea()->willReturn('TX');
        $memberAdministrativeArea->getCode()->willReturn('TX');

        $memberAdministrativeArea->getBelongsTo()->willReturn($zoneAdministrativeArea);
        $zoneAdministrativeArea->getType()->willReturn(ZoneInterface::TYPE_ADMINISTRATIVE_AREA);
        $zoneAdministrativeArea->getMembers()->willReturn(array($memberAdministrativeArea));

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

        $this->matchAll($address)->shouldReturn(array($zoneAdministrativeArea, $zoneCountry, $zoneZone));
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
