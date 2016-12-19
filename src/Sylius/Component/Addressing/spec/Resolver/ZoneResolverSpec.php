<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Addressing\Resolver;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Matcher\AddressZoneMatcherInterface;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Resolver\ZoneResolver;
use Sylius\Component\Addressing\Resolver\ZoneResolverInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class ZoneResolverSpec extends ObjectBehavior
{
    function let(RepositoryInterface $repository, AddressZoneMatcherInterface $addressZoneResolver)
    {
        $this->beConstructedWith($repository, $addressZoneResolver, [
            ZoneInterface::TYPE_PROVINCE,
            ZoneInterface::TYPE_COUNTRY,
            ZoneInterface::TYPE_ZONE,
        ]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ZoneResolver::class);
    }

    function it_implements_zone_resolver_interface()
    {
        $this->shouldImplement(ZoneResolverInterface::class);
    }

    function it_returns_null_if_there_are_no_zones(RepositoryInterface $repository, AddressInterface $address)
    {
        $repository->findAll()->willReturn([]);
        $this->match($address)->shouldReturn(null);
    }

    function it_should_match_address_by_zone(
        AddressZoneMatcherInterface $addressZoneResolver,
        RepositoryInterface $repository,
        AddressInterface $address,
        ZoneInterface $zone
    ) {
        $repository->findAll()->willReturn([$zone]);
        $zone->getType()->willReturn(ZoneInterface::TYPE_PROVINCE);
        $addressZoneResolver->addressBelongsToZone($address, $zone)->willReturn(true);

        $this->match($address)->shouldReturn($zone);
    }

    function it_should_match_address_by_zone_and_scope(
        AddressZoneMatcherInterface $addressZoneResolver,
        RepositoryInterface $repository,
        AddressInterface $address,
        ZoneInterface $zone
    ) {
        $repository->findBy(['scope' => 'shipping'])->shouldBeCalled()->willReturn([$zone]);
        $zone->getType()->willReturn(ZoneInterface::TYPE_PROVINCE);
        $addressZoneResolver->addressBelongsToZone($address, $zone)->willReturn(true);

        $this->match($address, 'shipping')->shouldReturn($zone);
    }

    function it_matches_address_from_province_when_many_are_found(
        AddressZoneMatcherInterface $addressZoneResolver,
        RepositoryInterface $repository,
        AddressInterface $address,
        ZoneInterface $zoneCountry,
        ZoneInterface $zoneProvince
    ) {
        $repository->findAll()->willReturn([$zoneCountry, $zoneProvince]);

        $zoneProvince->getType()->willReturn(ZoneInterface::TYPE_PROVINCE);
        $zoneCountry->getType()->willReturn(ZoneInterface::TYPE_COUNTRY);

        $addressZoneResolver->addressBelongsToZone($address, $zoneProvince)->willReturn(true);
        $addressZoneResolver->addressBelongsToZone($address, $zoneCountry)->willReturn(true);

        $this->match($address)->shouldReturn($zoneProvince);
    }

    function it_matches_address_from_province_when_many_are_found_by_scope(
        AddressZoneMatcherInterface $addressZoneResolver,
        RepositoryInterface $repository,
        AddressInterface $address,
        ZoneInterface $zoneCountry,
        ZoneInterface $zoneProvince
    ) {
        $repository->findBy(['scope' => 'shipping'])->willReturn([$zoneCountry, $zoneProvince]);

        $zoneCountry->getType()->willReturn(ZoneInterface::TYPE_COUNTRY);
        $zoneProvince->getType()->willReturn(ZoneInterface::TYPE_PROVINCE);

        $addressZoneResolver->addressBelongsToZone($address, $zoneProvince)->willReturn(true);
        $addressZoneResolver->addressBelongsToZone($address, $zoneCountry)->willReturn(true);

        $this->match($address, 'shipping')->shouldReturn($zoneProvince);
    }

    function it_matches_all_zones_with_given_address(
        AddressZoneMatcherInterface $addressZoneResolver,
        RepositoryInterface $repository,
        AddressInterface $address,
        ZoneInterface $zoneProvince,
        ZoneInterface $zoneCountry,
        ZoneInterface $zoneZone
    ) {
        $repository->findAll()->willReturn([$zoneProvince, $zoneCountry, $zoneZone]);

        $zoneProvince->getType()->willReturn(ZoneInterface::TYPE_PROVINCE);
        $zoneCountry->getType()->willReturn(ZoneInterface::TYPE_COUNTRY);
        $zoneZone->getType()->willReturn(ZoneInterface::TYPE_ZONE);

        $addressZoneResolver->addressBelongsToZone($address, $zoneProvince)->willReturn(true);
        $addressZoneResolver->addressBelongsToZone($address, $zoneCountry)->willReturn(true);
        $addressZoneResolver->addressBelongsToZone($address, $zoneZone)->willReturn(true);

        $this->matchAll($address)->shouldReturn([$zoneProvince, $zoneCountry, $zoneZone]);
    }

    function it_matches_all_zones_by_scope_when_one_zone_for_address_is_defined(
        AddressZoneMatcherInterface $addressZoneResolver,
        RepositoryInterface $repository,
        AddressInterface $address,
        ZoneInterface $zoneCountry
    ) {
        $repository->findBy(['scope' => 'shipping'])->willReturn([$zoneCountry]);

        $zoneCountry->getType()->willReturn(ZoneInterface::TYPE_COUNTRY);
        $addressZoneResolver->addressBelongsToZone($address, $zoneCountry)->willReturn(true);

        $this->matchAll($address, 'shipping')->shouldReturn([$zoneCountry]);
    }
}
