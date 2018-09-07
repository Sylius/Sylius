<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Addressing\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Sylius\Component\Addressing\Provider\ZoneCountriesProviderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class ZoneCountriesProviderSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $zoneRepository,
        RepositoryInterface $countryRepository,
        RepositoryInterface $provinceRepository
    ): void {
        $this->beConstructedWith($zoneRepository, $countryRepository, $provinceRepository);
    }

    function it_implements_zone_countries_provider_interface(): void
    {
        $this->shouldImplement(ZoneCountriesProviderInterface::class);
    }

    function it_returns_countries_from_zone_containing_countries(
        RepositoryInterface $countryRepository,
        ZoneInterface $zone,
        ZoneMemberInterface $unitedStatesZoneMember,
        ZoneMemberInterface $canadaZoneMember,
        CountryInterface $unitedStates,
        CountryInterface $canada
    ): void {
        $zone->getType()->willReturn(ZoneInterface::TYPE_COUNTRY);
        $zone->getMembers()->willReturn(new ArrayCollection([
            $unitedStatesZoneMember->getWrappedObject(),
            $canadaZoneMember->getWrappedObject(),
        ]));

        $unitedStatesZoneMember->getCode()->willReturn('US');
        $canadaZoneMember->getCode()->willReturn('CA');

        $countryRepository->findOneBy(['code' => 'US'])->willReturn($unitedStates);
        $countryRepository->findOneBy(['code' => 'CA'])->willReturn($canada);

        $this->getCountriesInWhichZoneOperates($zone)->shouldReturn([$unitedStates, $canada]);
    }

    function it_return_countries_from_zone_containing_other_zones_containing_countries(
        RepositoryInterface $zoneRepository,
        RepositoryInterface $countryRepository,
        ZoneInterface $zone,
        ZoneMemberInterface $americaZoneMember,
        ZoneMemberInterface $unitedStatesZoneMember,
        ZoneMemberInterface $canadaZoneMember,
        ZoneMemberInterface $mexicoZoneMember,
        ZoneInterface $america,
        CountryInterface $unitedStates,
        CountryInterface $canada,
        CountryInterface $mexico
    ): void {
        $zone->getType()->willReturn(ZoneInterface::TYPE_ZONE);
        $zone->getMembers()->willReturn(new ArrayCollection([
            $americaZoneMember->getWrappedObject(),
        ]));

        $americaZoneMember->getCode()->willReturn('AMERICA');
        $zoneRepository->findOneBy(['code' => 'AMERICA'])->willReturn($america);
        $america->getType()->willReturn(ZoneInterface::TYPE_COUNTRY);

        $america->getMembers()->willReturn(new ArrayCollection([
            $unitedStatesZoneMember->getWrappedObject(),
            $canadaZoneMember->getWrappedObject(),
            $mexicoZoneMember->getWrappedObject(),
        ]));

        $unitedStatesZoneMember->getCode()->willReturn('US');
        $canadaZoneMember->getCode()->willReturn('CA');
        $mexicoZoneMember->getCode()->willReturn('MX');

        $countryRepository->findOneBy(['code' => 'US'])->willReturn($unitedStates);
        $countryRepository->findOneBy(['code' => 'CA'])->willReturn($canada);
        $countryRepository->findOneBy(['code' => 'MX'])->willReturn($mexico);

        $this->getCountriesInWhichZoneOperates($zone)->shouldReturn([$unitedStates, $canada, $mexico]);
    }

    function it_returns_countries_from_zones_containing_provinces(
        RepositoryInterface $provinceRepository,
        ZoneInterface $zone,
        ZoneMemberInterface $texasZoneMember,
        ZoneMemberInterface $arizonaZoneMember,
        ZoneMemberInterface $ottawaZoneMember,
        ProvinceInterface $texas,
        ProvinceInterface $arizona,
        ProvinceInterface $ottawa,
        CountryInterface $unitedStates,
        CountryInterface $canada
    ): void {
        $zone->getType()->willReturn(ZoneInterface::TYPE_PROVINCE);
        $zone->getMembers()->willReturn(new ArrayCollection([
            $texasZoneMember->getWrappedObject(),
            $arizonaZoneMember->getWrappedObject(),
            $ottawaZoneMember->getWrappedObject(),
        ]));

        $texasZoneMember->getCode()->willReturn('TX');
        $arizonaZoneMember->getCode()->willReturn('AZ');
        $ottawaZoneMember->getCode()->willReturn('OTTAWA');

        $provinceRepository->findOneBy(['code' => 'TX'])->willReturn($texas);
        $provinceRepository->findOneBy(['code' => 'AZ'])->willReturn($arizona);
        $provinceRepository->findOneBy(['code' => 'OTTAWA'])->willReturn($ottawa);

        $texas->getCountry()->willReturn($unitedStates);
        $arizona->getCountry()->willReturn($unitedStates);
        $ottawa->getCountry()->willReturn($canada);

        $this->getCountriesInWhichZoneOperates($zone)->shouldReturn([$unitedStates, $canada]);
    }
}
