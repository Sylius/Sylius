<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Tests\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Sylius\Bundle\AddressingBundle\Factory\ZoneFactoryInterface;
use Sylius\Bundle\CoreBundle\Fixture\GeographicalFixture;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Intl\Intl;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class GeographicalFixtureTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function it_does_not_require_to_be_configured()
    {
        $this->assertConfigurationIsValid([[]]);
    }

    /**
     * @test
     */
    public function it_creates_all_known_countries_by_default()
    {
        $this->assertProcessedConfigurationEquals(
            [[]],
            ['countries' => array_keys(Intl::getRegionBundle()->getCountryNames())],
            'countries'
        );
    }

    /**
     * @test
     */
    public function it_replaces_predefined_countries_list_with_custom_ones()
    {
        $this->assertConfigurationIsValid(
            [['countries' => ['PL', 'DE', 'FR']]],
            'countries'
        );
    }

    /**
     * @test
     */
    public function it_creates_no_provinces_by_default()
    {
        $this->assertProcessedConfigurationEquals(
            [[]],
            ['provinces' => []],
            'provinces'
        );
    }

    /**
     * @test
     */
    public function it_creates_custom_provinces()
    {
        $this->assertConfigurationIsValid(
            [['provinces' => ['US' => ['AL' => 'Alabama']]]],
            'provinces'
        );
    }

    /**
     * @test
     */
    public function it_creates_no_zones_by_default()
    {
        $this->assertProcessedConfigurationEquals(
            [[]],
            ['zones' => []],
            'zones'
        );
    }

    /**
     * @test
     */
    public function it_creates_custom_countries_based_zone()
    {
        $this->assertConfigurationIsValid(
            [['zones' => ['EU' => ['name' => 'Some EU countries', 'countries' => ['PL', 'DE', 'FR']]]]],
            'zones'
        );
    }

    /**
     * @test
     */
    public function it_creates_custom_zones_based_zone()
    {
        $this->assertConfigurationIsValid(
            [['zones' => ['AMERICA' => ['name' => 'America', 'zones' => ['NORTH-AMERICA', 'SOUTH-AMERICA']]]]],
            'zones'
        );
    }

    /**
     * @test
     */
    public function it_creates_custom_provinces_based_zone()
    {
        $this->assertConfigurationIsValid(
            [['zones' => ['WEST-COAST' => ['name' => 'West Coast', 'provinces' => ['US-CA', 'US-OR', 'US-WA']]]]],
            'zones'
        );
    }

    /**
     * @test
     */
    public function its_zone_definition_has_exactly_one_type_of_members()
    {
        $this->assertPartialConfigurationIsInvalid(
            [['zones' => ['ZONE' => ['name' => 'zone']]]],
            'zones',
            'Zone must have only one type of members'
        );

        $this->assertPartialConfigurationIsInvalid(
            [['zones' => ['ZONE' => ['name' => 'zone', 'countries' => ['PL'], 'zones' => ['AMERICA']]]]],
            'zones',
            'Zone must have only one type of members'
        );

        $this->assertPartialConfigurationIsInvalid(
            [['zones' => ['ZONE' => ['name' => 'zone', 'countries' => ['PL'], 'provinces' => ['US-CA']]]]],
            'zones',
            'Zone must have only one type of members'
        );

        $this->assertPartialConfigurationIsInvalid(
            [['zones' => ['ZONE' => ['name' => 'zone', 'zones' => ['AMERICA'], 'provinces' => ['US-CA']]]]],
            'zones',
            'Zone must have only one type of members'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new GeographicalFixture(
            $this->getMockBuilder(FactoryInterface::class)->getMock(),
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(FactoryInterface::class)->getMock(),
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(ZoneFactoryInterface::class)->getMock(),
            $this->getMockBuilder(ObjectManager::class)->getMock()
        );
    }
}
