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

namespace spec\Sylius\Bundle\CoreBundle\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Sylius\Component\Addressing\Factory\ZoneFactoryInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class GeographicalFixtureSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $countryFactory,
        ObjectManager $countryManager,
        FactoryInterface $provinceFactory,
        ObjectManager $provinceManager,
        ZoneFactoryInterface $zoneFactory,
        ObjectManager $zoneManager
    ): void {
        $this->beConstructedWith(
            $countryFactory,
            $countryManager,
            $provinceFactory,
            $provinceManager,
            $zoneFactory,
            $zoneManager
        );
    }

    function it_is_a_fixture(): void
    {
        $this->shouldImplement(FixtureInterface::class);
    }

    function it_creates_and_persist_a_country(
        FactoryInterface $countryFactory,
        ObjectManager $countryManager,
        CountryInterface $country
    ): void {
        $countryFactory->createNew()->willReturn($country);
        $country->setCode('PL')->shouldBeCalled();
        $country->enable()->shouldBeCalled();

        $countryManager->persist($country)->shouldBeCalled();
        $countryManager->flush()->shouldBeCalled();

        $this->load(['countries' => ['PL'], 'provinces' => [], 'zones' => []]);
    }

    function it_creates_and_persist_a_country_province(
        FactoryInterface $countryFactory,
        ObjectManager $countryManager,
        FactoryInterface $provinceFactory,
        ObjectManager $provinceManager,
        CountryInterface $country,
        ProvinceInterface $province
    ): void {
        $countryFactory->createNew()->willReturn($country);
        $country->setCode('PL')->shouldBeCalled();
        $country->enable()->shouldBeCalled();

        $provinceFactory->createNew()->willReturn($province);
        $province->setCode('PL-SL')->shouldBeCalled();
        $province->setName('Silesia')->shouldBeCalled();

        $country->addProvince($province)->shouldBeCalled();

        $countryManager->persist($country)->shouldBeCalled();
        $provinceManager->persist($province)->shouldBeCalled();

        $countryManager->flush()->shouldBeCalled();
        $provinceManager->flush()->shouldBeCalled();

        $this->load(['countries' => ['PL'], 'provinces' => ['PL' => ['PL-SL' => 'Silesia']], 'zones' => []]);
    }

    function it_throws_an_exception_if_trying_to_create_a_province_for_unexisting_country(): void
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('load', [['countries' => [], 'provinces' => ['PL' => ['PL-SL' => 'Silesia']], 'zones' => []]]);
    }

    function it_creates_and_persist_a_country_and_a_zone_containing_it(
        FactoryInterface $countryFactory,
        ObjectManager $countryManager,
        ZoneFactoryInterface $zoneFactory,
        ObjectManager $zoneManager,
        CountryInterface $country,
        ZoneInterface $zone
    ): void {
        $countryFactory->createNew()->willReturn($country);
        $country->setCode('PL')->shouldBeCalled();
        $country->enable()->shouldBeCalled();

        $zoneFactory->createWithMembers(['PL'])->willReturn($zone);
        $zone->setCode('POLAND')->shouldBeCalled();
        $zone->setName('Poland')->shouldBeCalled();
        $zone->setType(ZoneInterface::TYPE_COUNTRY)->shouldBeCalled();

        $countryManager->persist($country)->shouldBeCalled();
        $zoneManager->persist($zone)->shouldBeCalled();

        $countryManager->flush()->shouldBeCalled();
        $zoneManager->flush()->shouldBeCalled();

        $this->load(['countries' => ['PL'], 'provinces' => [], 'zones' => [
            'POLAND' => [
                'name' => 'Poland',
                'countries' => ['PL'],
                'provinces' => [],
                'zones' => [],
            ],
        ]]);
    }

    function it_creates_and_persist_a_country_and_a_zone_with_scope_containing_it(
        FactoryInterface $countryFactory,
        ObjectManager $countryManager,
        ZoneFactoryInterface $zoneFactory,
        ObjectManager $zoneManager,
        CountryInterface $country,
        ZoneInterface $zone
    ): void {
        $countryFactory->createNew()->willReturn($country);
        $country->setCode('PL')->shouldBeCalled();
        $country->enable()->shouldBeCalled();

        $zoneFactory->createWithMembers(['PL'])->willReturn($zone);
        $zone->setCode('POLAND')->shouldBeCalled();
        $zone->setName('Poland')->shouldBeCalled();
        $zone->setType(ZoneInterface::TYPE_COUNTRY)->shouldBeCalled();
        $zone->setScope('tax')->shouldBeCalled();

        $countryManager->persist($country)->shouldBeCalled();
        $zoneManager->persist($zone)->shouldBeCalled();

        $countryManager->flush()->shouldBeCalled();
        $zoneManager->flush()->shouldBeCalled();

        $this->load(['countries' => ['PL'], 'provinces' => [], 'zones' => [
            'POLAND' => [
                'name' => 'Poland',
                'countries' => ['PL'],
                'provinces' => [],
                'zones' => [],
                'scope' => 'tax',
            ],
        ]]);
    }

    function it_creates_and_persist_a_country_province_and_a_zone_containing_it(
        FactoryInterface $countryFactory,
        ObjectManager $countryManager,
        FactoryInterface $provinceFactory,
        ObjectManager $provinceManager,
        ZoneFactoryInterface $zoneFactory,
        ObjectManager $zoneManager,
        CountryInterface $country,
        ProvinceInterface $province,
        ZoneInterface $zone
    ): void {
        $countryFactory->createNew()->willReturn($country);
        $country->setCode('PL')->shouldBeCalled();
        $country->enable()->shouldBeCalled();

        $provinceFactory->createNew()->willReturn($province);
        $province->setCode('PL-SL')->shouldBeCalled();
        $province->setName('Silesia')->shouldBeCalled();

        $country->addProvince($province)->shouldBeCalled();

        $zoneFactory->createWithMembers(['PL-SL'])->willReturn($zone);
        $zone->setCode('SILESIA')->shouldBeCalled();
        $zone->setName('Silesia')->shouldBeCalled();
        $zone->setType(ZoneInterface::TYPE_PROVINCE)->shouldBeCalled();

        $countryManager->persist($country)->shouldBeCalled();
        $provinceManager->persist($province)->shouldBeCalled();
        $zoneManager->persist($zone)->shouldBeCalled();

        $countryManager->flush()->shouldBeCalled();
        $provinceManager->flush()->shouldBeCalled();
        $zoneManager->flush()->shouldBeCalled();

        $this->load(['countries' => ['PL'], 'provinces' => ['PL' => ['PL-SL' => 'Silesia']], 'zones' => [
            'SILESIA' => [
                'name' => 'Silesia',
                'countries' => [],
                'provinces' => ['PL-SL'],
                'zones' => [],
            ],
        ]]);
    }

    function it_creates_and_persist_a_country_type_zone_and_a_zone_containing_it(
        FactoryInterface $countryFactory,
        ObjectManager $countryManager,
        ZoneFactoryInterface $zoneFactory,
        ObjectManager $zoneManager,
        CountryInterface $country,
        ZoneInterface $countryTypeZone,
        ZoneInterface $zoneTypeZone
    ): void {
        $countryFactory->createNew()->willReturn($country);
        $country->setCode('PL')->shouldBeCalled();
        $country->enable()->shouldBeCalled();

        $zoneFactory->createWithMembers(['PL'])->willReturn($countryTypeZone);
        $countryTypeZone->setCode('POLAND')->shouldBeCalled();
        $countryTypeZone->setName('Poland')->shouldBeCalled();
        $countryTypeZone->setType(ZoneInterface::TYPE_COUNTRY)->shouldBeCalled();

        $zoneFactory->createWithMembers(['POLAND'])->willReturn($zoneTypeZone);
        $zoneTypeZone->setCode('YO-DAWG')->shouldBeCalled();
        $zoneTypeZone->setName('Yo dawg')->shouldBeCalled();
        $zoneTypeZone->setType(ZoneInterface::TYPE_ZONE)->shouldBeCalled();

        $countryManager->persist($country)->shouldBeCalled();
        $zoneManager->persist($countryTypeZone)->shouldBeCalled();
        $zoneManager->persist($zoneTypeZone)->shouldBeCalled();

        $countryManager->flush()->shouldBeCalled();
        $zoneManager->flush()->shouldBeCalled();

        $this->load(['countries' => ['PL'], 'provinces' => [], 'zones' => [
            'POLAND' => [
                'name' => 'Poland',
                'countries' => ['PL'],
                'provinces' => [],
                'zones' => [],
            ],
            'YO-DAWG' => [
                'name' => 'Yo dawg',
                'countries' => [],
                'provinces' => [],
                'zones' => ['POLAND'],
            ],
        ]]);
    }

    function it_throws_an_exception_if_trying_to_create_a_zone_with_unexisting_country(): void
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('load', [['countries' => [], 'provinces' => [], 'zones' => [
            'ZONE' => [
                'name' => 'Zone',
                'countries' => ['PL'],
                'provinces' => [],
                'zones' => [],
            ],
        ]]]);
    }

    function it_throws_an_exception_if_trying_to_create_a_zone_with_unexisting_province(): void
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('load', [['countries' => [], 'provinces' => [], 'zones' => [
            'ZONE' => [
                'name' => 'Zone',
                'countries' => [],
                'provinces' => ['PL-SL'],
                'zones' => [],
            ],
        ]]]);
    }

    function it_throws_an_exception_if_trying_to_create_a_zone_with_unexisting_zone(): void
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('load', [['countries' => [], 'provinces' => [], 'zones' => [
            'ZONE' => [
                'name' => 'Zone',
                'countries' => [],
                'provinces' => [],
                'zones' => ['DAWG'],
            ],
        ]]]);
    }
}
