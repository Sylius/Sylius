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

namespace Tests\Sylius\Bundle\CoreBundle\Fixture;

use Doctrine\Persistence\ObjectManager;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Fixture\GeographicalFixture;
use Sylius\Component\Addressing\Factory\ZoneFactoryInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Symfony\Component\Intl\Countries;

final class GeographicalFixtureTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    #[Test]
    public function fixture_does_not_need_to_be_configured(): void
    {
        $this->assertConfigurationIsValid([[]]);
    }

    #[Test]
    public function countries_are_set_to_all_known_countries_by_default(): void
    {
        $this->assertProcessedConfigurationEquals(
            [[]],
            ['countries' => array_keys(Countries::getNames())],
            'countries',
        );
    }

    #[Test]
    public function countries_can_be_replaced_with_custom_ones(): void
    {
        $this->assertConfigurationIsValid(
            [['countries' => ['PL', 'DE', 'FR']]],
            'countries',
        );
    }

    #[Test]
    public function provinces_are_empty_by_default(): void
    {
        $this->assertProcessedConfigurationEquals(
            [[]],
            ['provinces' => []],
            'provinces',
        );
    }

    #[Test]
    public function provinces_can_be_set(): void
    {
        $this->assertConfigurationIsValid(
            [['provinces' => ['US' => ['AL' => 'Alabama']]]],
            'provinces',
        );
    }

    #[Test]
    public function zones_are_empty_by_default(): void
    {
        $this->assertProcessedConfigurationEquals(
            [[]],
            ['zones' => []],
            'zones',
        );
    }

    #[Test]
    public function zones_can_be_defined_as_country_based(): void
    {
        $this->assertConfigurationIsValid(
            [['zones' => ['EU' => ['name' => 'Some EU countries', 'countries' => ['PL', 'DE', 'FR']]]]],
            'zones',
        );
    }

    #[Test]
    public function zones_can_have_scopes_based(): void
    {
        $this->assertConfigurationIsValid(
            [['zones' => ['EU' => ['name' => 'Some EU countries', 'countries' => ['PL', 'DE', 'FR'], 'scope' => 'tax']]]],
            'zones',
        );
    }

    #[Test]
    public function zones_can_be_defined_as_province_based(): void
    {
        $this->assertConfigurationIsValid(
            [['zones' => ['WEST-COAST' => ['name' => 'West Coast', 'provinces' => ['US-CA', 'US-OR', 'US-WA']]]]],
            'zones',
        );
    }

    #[Test]
    public function zones_can_be_defined_as_zone_based(): void
    {
        $this->assertConfigurationIsValid(
            [['zones' => ['AMERICA' => ['name' => 'America', 'zones' => ['NORTH-AMERICA', 'SOUTH-AMERICA']]]]],
            'zones',
        );
    }

    #[Test]
    public function zone_can_be_defined_with_exactly_one_kind_of_members(): void
    {
        $this->assertPartialConfigurationIsInvalid(
            [['zones' => ['ZONE' => ['name' => 'zone']]]],
            'zones',
            'Zone must have only one type of members',
        );

        $this->assertPartialConfigurationIsInvalid(
            [['zones' => ['ZONE' => ['name' => 'zone', 'countries' => ['PL'], 'zones' => ['AMERICA']]]]],
            'zones',
            'Zone must have only one type of members',
        );

        $this->assertPartialConfigurationIsInvalid(
            [['zones' => ['ZONE' => ['name' => 'zone', 'countries' => ['PL'], 'provinces' => ['US-CA']]]]],
            'zones',
            'Zone must have only one type of members',
        );

        $this->assertPartialConfigurationIsInvalid(
            [['zones' => ['ZONE' => ['name' => 'zone', 'zones' => ['AMERICA'], 'provinces' => ['US-CA']]]]],
            'zones',
            'Zone must have only one type of members',
        );
    }

    protected function getConfiguration(): GeographicalFixture
    {
        return new GeographicalFixture(
            $this->getMockBuilder(FactoryInterface::class)->getMock(),
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(FactoryInterface::class)->getMock(),
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(ZoneFactoryInterface::class)->getMock(),
            $this->getMockBuilder(ObjectManager::class)->getMock(),
        );
    }
}
