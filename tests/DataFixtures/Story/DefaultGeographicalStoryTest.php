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

namespace Sylius\Tests\DataFixtures\Story;

use Sylius\Bundle\CoreBundle\DataFixtures\Story\DefaultCurrenciesStoryInterface;
use Sylius\Component\Addressing\Model\Zone;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class DefaultGeographicalStoryTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    public function it_creates_default_zones(): void
    {
        /** @var DefaultCurrenciesStoryInterface $defaultCurrenciesStory */
        $defaultCurrenciesStory = self::getContainer()->get('sylius.data_fixtures.story.default_geographical');

        $defaultCurrenciesStory->build();

        $this->assertZoneIsOnDatabase('United_States_Of_America');
        $this->assertZoneIsOnDatabase('Rest_Of_The_World');
    }

    /** @test */
    public function it_creates_united_states_of_america_zone_with_us_country(): void
    {
        /** @var DefaultCurrenciesStoryInterface $defaultCurrenciesStory */
        $defaultCurrenciesStory = self::getContainer()->get('sylius.data_fixtures.story.default_geographical');

        $defaultCurrenciesStory->build();

        $zone = $this->getZoneByCode('United_States_Of_America');
        $this->assertNotNull($zone);
        $this->assertCount(1, $zone->getMembers());
        $this->assertEquals('US', $zone->getMembers()->first()->getCode());
    }

    /** @test */
    public function it_creates_rest_of_the_world_zone_with_some_countries(): void
    {
        /** @var DefaultCurrenciesStoryInterface $defaultCurrenciesStory */
        $defaultCurrenciesStory = self::getContainer()->get('sylius.data_fixtures.story.default_geographical');

        $defaultCurrenciesStory->build();

        $zone = $this->getZoneByCode('Rest_Of_The_World');
        $this->assertNotNull($zone);
        $this->assertCount(11, $zone->getMembers());
        $this->assertEquals('FR', $zone->getMembers()->first()->getCode());
        $this->assertEquals('DE', $zone->getMembers()->get(1)->getCode());
        $this->assertEquals('AU', $zone->getMembers()->get(2)->getCode());
        $this->assertEquals('CA', $zone->getMembers()->get(3)->getCode());
        $this->assertEquals('MX', $zone->getMembers()->get(4)->getCode());
        $this->assertEquals('NZ', $zone->getMembers()->get(5)->getCode());
        $this->assertEquals('PT', $zone->getMembers()->get(6)->getCode());
        $this->assertEquals('ES', $zone->getMembers()->get(7)->getCode());
        $this->assertEquals('CN', $zone->getMembers()->get(8)->getCode());
        $this->assertEquals('GB', $zone->getMembers()->get(9)->getCode());
        $this->assertEquals('PL', $zone->getMembers()->last()->getCode());
    }

    private function getZoneByCode(string $code): ?Zone
    {
        /** @var RepositoryInterface $zoneRepository */
        $zoneRepository = self::getContainer()->get('sylius.repository.zone');

        return $zoneRepository->findOneBy(['code' => $code]);
    }

    private function assertZoneIsOnDatabase(string $zoneCode)
    {
        $zone = $this->getZoneByCode($zoneCode);
        $this->assertNotNull($zone, sprintf('Zone "%s" should be on database but it does not.', $zoneCode));
    }
}
