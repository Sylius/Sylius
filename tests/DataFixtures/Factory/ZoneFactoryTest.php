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

namespace Sylius\Tests\DataFixtures\Factory;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ZoneFactory;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ZoneMemberFactory;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class ZoneFactoryTest extends KernelTestCase
{
    use ResetDatabase;
    use Factories;

    /** @test */
    function it_creates_zone_with_random_code(): void
    {
        $zone = ZoneFactory::createOne();

        $this->assertInstanceOf(ZoneInterface::class, $zone->object());
        $this->assertNotNull($zone->getCode());
    }

    /** @test */
    function it_creates_zone_with_given_code(): void
    {
        $zone = ZoneFactory::new()->withCode('world')->create();

        $this->assertEquals('world', $zone->getCode());
    }

    /** @test */
    function it_creates_zone_with_new_members(): void
    {
        $zone = ZoneFactory::new()->withMembers(['united_states', 'france'])->create();

        $this->assertEquals('zone', $zone->getType());
        $this->assertCount(2, $zone->getMembers());
    }

    /** @test */
    function it_creates_zone_with_existing_proxy_members(): void
    {
        $firstZoneMember = ZoneMemberFactory::new()->withCode('zone_a')->create();
        $secondZoneMember = ZoneMemberFactory::new()->withCode('zone_b')->create();

        $zone = ZoneFactory::new()->withMembers([$firstZoneMember, $secondZoneMember])->create();

        $this->assertEquals('zone', $zone->getType());
        $this->assertCount(2, $zone->getMembers());

        $this->assertTrue($zone->hasMember($firstZoneMember->object()));
        $this->assertTrue($zone->hasMember($secondZoneMember->object()));
    }

    /** @test */
    function it_creates_zone_with_existing_members(): void
    {
        $firstZoneMember = ZoneMemberFactory::new()->withCode('zone_a')->create()->object();
        $secondZoneMember = ZoneMemberFactory::new()->withCode('zone_b')->create()->object();

        $zone = ZoneFactory::new()->withMembers([$firstZoneMember, $secondZoneMember])->create();

        $this->assertEquals('zone', $zone->getType());
        $this->assertCount(2, $zone->getMembers());

        $this->assertTrue($zone->hasMember($firstZoneMember));
        $this->assertTrue($zone->hasMember($secondZoneMember));
    }

    /** @test */
    function it_creates_zone_with_new_countries(): void
    {
        $zone = ZoneFactory::new()->withCountries(['FR', 'EN'])->create();

        $this->assertEquals('country', $zone->getType());
        $this->assertCount(2, $zone->getMembers());
    }

    /** @test */
    function it_creates_zone_with_existing_proxy_countries(): void
    {
        $firstZoneMember = ZoneMemberFactory::new()->withCode('FR')->create();
        $secondZoneMember = ZoneMemberFactory::new()->withCode('EN')->create();

        $zone = ZoneFactory::new()->withCountries([$firstZoneMember, $secondZoneMember])->create();

        $this->assertEquals('country', $zone->getType());
        $this->assertCount(2, $zone->getMembers());

        $this->assertTrue($zone->hasMember($firstZoneMember->object()));
        $this->assertTrue($zone->hasMember($secondZoneMember->object()));
    }

    /** @test */
    function it_creates_zone_with_existing_countries(): void
    {
        $firstZoneMember = ZoneMemberFactory::new()->withCode('FR')->create()->object();
        $secondZoneMember = ZoneMemberFactory::new()->withCode('EN')->create()->object();

        $zone = ZoneFactory::new()->withCountries([$firstZoneMember, $secondZoneMember])->create();

        $this->assertEquals('country', $zone->getType());
        $this->assertCount(2, $zone->getMembers());

        $this->assertTrue($zone->hasMember($firstZoneMember));
        $this->assertTrue($zone->hasMember($secondZoneMember));
    }

    /** @test */
    function it_creates_zone_with_new_provinces(): void
    {
        $zone = ZoneFactory::new()->withProvinces(['US-TX', 'US-ME'])->create();

        $this->assertEquals('province', $zone->getType());
        $this->assertCount(2, $zone->getMembers());
    }

    /** @test */
    function it_creates_zone_with_existing_proxy_provinces(): void
    {
        $firstZoneMember = ZoneMemberFactory::new()->withCode('US-TX')->create();
        $secondZoneMember = ZoneMemberFactory::new()->withCode('US-ME')->create();

        $zone = ZoneFactory::new()->withProvinces([$firstZoneMember, $secondZoneMember])->create();

        $this->assertEquals('province', $zone->getType());
        $this->assertCount(2, $zone->getMembers());

        $this->assertTrue($zone->hasMember($firstZoneMember->object()));
        $this->assertTrue($zone->hasMember($secondZoneMember->object()));
    }

    /** @test */
    function it_creates_zone_with_existing_provinces(): void
    {
        $firstZoneMember = ZoneMemberFactory::new()->withCode('US-TX')->create()->object();
        $secondZoneMember = ZoneMemberFactory::new()->withCode('US-ME')->create()->object();

        $zone = ZoneFactory::new()->withProvinces([$firstZoneMember, $secondZoneMember])->create();

        $this->assertEquals('province', $zone->getType());
        $this->assertCount(2, $zone->getMembers());

        $this->assertTrue($zone->hasMember($firstZoneMember));
        $this->assertTrue($zone->hasMember($secondZoneMember));
    }
}
