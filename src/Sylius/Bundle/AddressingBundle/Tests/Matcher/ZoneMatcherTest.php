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

namespace Sylius\Bundle\AddressingBundle\Tests\Matcher;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Sylius\Component\Addressing\Model\Address;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ZoneMatcherTest extends KernelTestCase
{
    protected function setUp(): void
    {
        $this->loadFixtures();
    }

    /** @test */
    public function it_matches_all_zones_with_their_parents(): void
    {
        $address = new Address();
        $address->setCountryCode('PL');
        $address->setProvinceCode('MA');

        $zoneMatcher = self::getContainer()->get('sylius.zone_matcher');
        $matchedZones = [];

        foreach ($zoneMatcher->matchAll($address) as $zone) {
            $matchedZones[$zone->getCode()] = $zone;
        }

        $this->assertCount(4, $matchedZones);
        $this->assertArrayHasKey('EU', $matchedZones);
        $this->assertArrayHasKey('VISEGRAD_GROUP', $matchedZones);
        $this->assertArrayHasKey('PL', $matchedZones);
        $this->assertArrayHasKey('NATO', $matchedZones);
    }

    /** @test */
    public function it_matches_all_zones_with_their_parents_with_restricting_by_scope(): void
    {
        $address = new Address();
        $address->setCountryCode('PL');
        $address->setProvinceCode('MA');

        $zoneMatcher = self::getContainer()->get('sylius.zone_matcher');
        $matchedZones = [];

        foreach ($zoneMatcher->matchAll($address, 'nato') as $zone) {
            $matchedZones[$zone->getCode()] = $zone;
        }

        $this->assertCount(1, $matchedZones);
        $this->assertArrayHasKey('NATO', $matchedZones);
    }

    private function loadFixtures(): void
    {
        /** @var LoaderInterface $fixtureLoader */
        $fixtureLoader = self::getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');

        /** @var EntityManagerInterface $manager */
        $manager = self::getContainer()->get('doctrine.orm.default_entity_manager');

        (new ORMPurger($manager))->purge();

        $fixtureLoader->load([
            __DIR__ . '/ZoneMatcherTest/fixtures.yaml',
        ], [], [], PurgeMode::createDeleteMode());
    }
}
