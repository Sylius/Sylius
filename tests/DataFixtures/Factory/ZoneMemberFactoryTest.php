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
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class ZoneMemberFactoryTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    function it_creates_zone_member(): void
    {
        $zoneMember = ZoneMemberFactory::createOne();

        $this->assertInstanceOf(ZoneMemberInterface::class, $zoneMember->object());
        $this->assertNotNull($zoneMember->getCode());
    }

    /** @test */
    function it_creates_zone_member_with_given_code(): void
    {
        $zoneMember = ZoneMemberFactory::new()->withCode('united_states')->create();

        $this->assertEquals('united_states', $zoneMember->getCode());
    }

    /** @test */
    function it_creates_zone_member_with_new_parent_code(): void
    {
        $zoneMember = ZoneMemberFactory::new()->belongsTo('world')->create();

        $this->assertEquals('world', $zoneMember->getBelongsTo()->getCode());
    }

    /** @test */
    function it_creates_zone_member_with_existing_proxy_parent(): void
    {
        $parentZone = ZoneFactory::new()->create();
        $zoneMember = ZoneMemberFactory::new()->belongsTo($parentZone)->create();

        $this->assertEquals($parentZone->object(), $zoneMember->getBelongsTo());
    }

    /** @test */
    function it_creates_zone_member_with_existing_parent(): void
    {
        $parentZone = ZoneFactory::new()->create()->object();
        $zoneMember = ZoneMemberFactory::new()->belongsTo($parentZone)->create();

        $this->assertEquals($parentZone, $zoneMember->getBelongsTo());
    }
}
