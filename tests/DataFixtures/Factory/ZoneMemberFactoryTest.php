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
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class ZoneMemberFactoryTest extends KernelTestCase
{
    use ResetDatabase;
    use Factories;

    /** @test */
    function it_creates_zone_members(): void
    {
        $zoneMember = ZoneMemberFactory::new()->create();

        $this->assertInstanceOf(ZoneMemberInterface::class, $zoneMember->object());
    }

    /** @test */
    function it_creates_zone_members_with_codes(): void
    {
        $zoneMember = ZoneMemberFactory::new()->withCode('united_states')->create();

        $this->assertEquals('united_states', $zoneMember->getCode());

        $zoneMember = ZoneMemberFactory::new()->create();

        $this->assertNotNull($zoneMember->getCode());
    }

    /** @test */
    function it_creates_zone_members_with_parents(): void
    {
        $parentZone = ZoneFactory::new()->withoutPersisting()->create();
        $zoneMember = ZoneMemberFactory::new()->withoutPersisting()->belongsTo($parentZone)->create();

        $this->assertEquals($parentZone->object(), $zoneMember->getBelongsTo());

        $parentZone = ZoneFactory::new()->withoutPersisting()->create()->object();
        $zoneMember = ZoneMemberFactory::new()->withoutPersisting()->belongsTo($parentZone)->create();

        $this->assertEquals($parentZone, $zoneMember->getBelongsTo());

        $zoneMember = ZoneMemberFactory::new()->withoutPersisting()->belongsTo('world')->create();

        $this->assertEquals('world', $zoneMember->getBelongsTo()->getCode());
    }
}
