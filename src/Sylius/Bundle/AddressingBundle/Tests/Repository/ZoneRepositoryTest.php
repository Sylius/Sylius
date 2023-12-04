<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AddressingBundle\Tests\Repository;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Sylius\Component\Addressing\Model\Address;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Repository\ZoneRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ZoneRepositoryTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures();
    }

    /** @test */
    public function it_finds_a_single_zone_by_address_and_type(): void
    {
        $address = new Address();
        $address->setCountryCode('PL');
        $address->setProvinceCode('MA');

        $repository = $this->getRepository();
        $zoneByProvince = $repository->findOneByAddressAndType($address, ZoneInterface::TYPE_PROVINCE);
        $zoneByCountry = $repository->findOneByAddressAndType($address, ZoneInterface::TYPE_COUNTRY);
        $zoneByZone = $repository->findOneByAddressAndType($address, ZoneInterface::TYPE_ZONE);

        $this->assertNotNull($zoneByProvince);
        $this->assertNotNull($zoneByCountry);
        $this->assertNull($zoneByZone);
        $this->assertSame('POLISH_PROVINCES', $zoneByProvince->getCode());
        $this->assertSame('EU', $zoneByCountry->getCode());
    }

    /** @test */
    public function it_finds_all_zones_for_a_given_address_with_only_country(): void
    {
        $address = new Address();
        $address->setCountryCode('PL');

        $repository = $this->getRepository();
        $zones = [];

        foreach ($repository->findByAddress($address) as $zone) {
            $zones[$zone->getCode()] = $zone;
        }

        $this->assertCount(2, $zones);
        $this->assertArrayHasKey('EU', $zones);
        $this->assertArrayHasKey('VISEGRAD_GROUP', $zones);
    }

    /** @test */
    public function it_finds_all_zones_for_a_given_address_with_restricting_by_scope_if_provided(): void
    {
        $address = new Address();
        $address->setCountryCode('PL');
        $address->setProvinceCode('MA');

        $repository = $this->getRepository();
        $zones = [];

        foreach ($repository->findByAddress($address, 'tax') as $zone) {
            $zones[$zone->getCode()] = $zone;
        }

        $this->assertCount(3, $zones);
        $this->assertArrayHasKey('EU', $zones);
        $this->assertArrayHasKey('VISEGRAD_GROUP', $zones);
        $this->assertArrayHasKey('POLISH_PROVINCES', $zones);
    }

    /** @test */
    public function it_finds_all_zones_by_passing_a_zone_member(): void
    {
        $repository = $this->getRepository();
        $zones = [];

        $visegradGroupZone = $repository->findOneBy(['code' => 'VISEGRAD_GROUP']);
        foreach ($repository->findByMembers([$visegradGroupZone]) as $zone) {
            $zones[$zone->getCode()] = $zone;
        }

        $this->assertCount(2, $zones);
        $this->assertArrayHasKey('EU_MIDDLE', $zones);
        $this->assertArrayHasKey('NATO', $zones);
    }

    /** @test */
    public function it_finds_all_zones_by_passing_a_zone_member_with_restricting_by_scope_if_provided(): void
    {
        $repository = $this->getRepository();
        $zones = [];

        /** @var ZoneInterface $visegradGroupZone */
        $visegradGroupZone = $repository->findOneBy(['code' => 'VISEGRAD_GROUP']);

        foreach ($repository->findByMembers([$visegradGroupZone], 'tax') as $zone) {
            $zones[$zone->getCode()] = $zone;
        }

        $this->assertCount(1, $zones);
        $this->assertArrayHasKey('EU_MIDDLE', $zones);
        $this->assertArrayNotHasKey('NATO', $zones);
    }

    private function getRepository(): ZoneRepositoryInterface
    {
        /** @var ZoneRepositoryInterface $repository */
        $repository = self::getContainer()->get('sylius.repository.zone');

        return $repository;
    }

    private function loadFixtures(): void
    {
        /** @var LoaderInterface $fixtureLoader */
        $fixtureLoader = self::getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');

        /** @var EntityManagerInterface $manager */
        $manager = self::getContainer()->get('doctrine.orm.default_entity_manager');

        (new ORMPurger($manager))->purge();

        $fixtureLoader->load([
            __DIR__ . '/ZoneRepositoryTest/fixtures.yaml',
        ], [], [], PurgeMode::createDeleteMode());
    }
}
