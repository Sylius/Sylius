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
use Sylius\Component\Addressing\Resolver\AddressZoneResolver;
use Sylius\Component\Addressing\Resolver\AddressZoneResolverInterface;
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
final class AddressZoneResolverSpec extends ObjectBehavior
{
    function let(RepositoryInterface $repository)
    {
        $this->beConstructedWith($repository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AddressZoneResolver::class);
    }

    function it_implements_zone_matcher_interface()
    {
        $this->shouldImplement(AddressZoneResolverInterface::class);
    }

    function it_resolves_address_zone_by_province(
        ProvinceInterface $province,
        AddressInterface $address,
        ZoneMemberInterface $memberProvince,
        ZoneInterface $zone
    ) {
        $province->getCode()->willReturn('DU');
        $address->getProvinceCode()->willReturn('DU');
        $memberProvince->getCode()->willReturn('DU');

        $zone->getType()->willReturn(ZoneInterface::TYPE_PROVINCE);
        $zone->getMembers()->willReturn([$memberProvince]);
        $memberProvince->getBelongsTo()->willReturn($zone);

        $this->addressBelongsToZone($address, $zone)->shouldReturn(true);
    }

    function it_resolves_address_zone_by_country(
        CountryInterface $country,
        AddressInterface $address,
        ZoneMemberInterface $memberCountry,
        ZoneInterface $zone
    ) {
        $country->getCode()->willReturn('IE');
        $address->getCountryCode()->willReturn('IE');
        $memberCountry->getCode()->willReturn('IE');
        $zone->getType()->willReturn(ZoneInterface::TYPE_COUNTRY);
        $zone->getMembers()->willReturn([$memberCountry]);
        $memberCountry->getBelongsTo()->willReturn($zone);

        $this->addressBelongsToZone($address, $zone)->shouldReturn(true);
    }

    function it_resolves_address_zone_for_nested_zones(
        RepositoryInterface $repository,
        CountryInterface $country,
        AddressInterface $address,
        ZoneMemberInterface $memberCountry,
        ZoneMemberInterface $memberZone,
        ZoneInterface $subZone,
        ZoneInterface $rootZone
    ) {
        $country->getCode()->willReturn('IE');

        $address->getCountryCode()->willReturn('IE');
        $memberCountry->getCode()->willReturn('IE');
        $subZone->getMembers()->willReturn([$memberCountry]);
        $subZone->getType()->willReturn(ZoneInterface::TYPE_COUNTRY);
        $subZone->getCode()->willReturn('Ireland');
        $memberZone->getCode()->willReturn('Ireland');
        $rootZone->getMembers()->willReturn([$memberZone]);
        $rootZone->getType()->willReturn(ZoneInterface::TYPE_ZONE);

        $memberCountry->getBelongsTo()->willReturn($subZone);
        $memberZone->getBelongsTo()->willReturn($rootZone);
        $repository->findOneBy(['code' => 'Ireland'])->willReturn($subZone);
        $repository->findAll()->willReturn([$rootZone]);

        $this->addressBelongsToZone($address, $rootZone)->shouldReturn(true);
    }
}
