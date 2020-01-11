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

namespace Sylius\Bundle\CoreBundle\Tests\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Fixture\GeographicalFixture;
use Sylius\Component\Addressing\Factory\ZoneFactoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Intl\Intl;

final class GeographicalFixtureTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function fixture_does_not_need_to_be_configured(): void
    {
        $this->assertConfigurationIsValid([[]]);
    }

    /**
     * @test
     */
    public function countries_are_set_to_all_known_countries_by_default(): void
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
    public function countries_can_be_replaced_with_custom_ones(): void
    {
        $this->assertConfigurationIsValid(
            [['countries' => ['PL', 'DE', 'FR']]],
            'countries'
        );
    }

    /**
     * @test
     */
    public function provinces_are_empty_by_default(): void
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
    public function provinces_can_be_set(): void
    {
        $this->assertConfigurationIsValid(
            [['provinces' => ['US' => ['AL' => 'Alabama']]]],
            'provinces'
        );
    }

    /**
     * @test
     */
    public function zones_are_empty_by_default(): void
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
    public function zones_can_be_defined_as_country_based(): void
    {
        $this->assertConfigurationIsValid(
            [['zones' => ['EU' => ['name' => 'Some EU countries', 'countries' => ['PL', 'DE', 'FR']]]]],
            'zones'
        );
    }

    /**
     * @test
     */
    public function zones_can_have_scopes_based(): void
    {
        $this->assertConfigurationIsValid(
            [['zones' => ['EU' => ['name' => 'Some EU countries', 'countries' => ['PL', 'DE', 'FR'], 'scope' => 'tax']]]],
            'zones'
        );
    }

    /**
     * @test
     */
    public function zones_can_be_defined_as_province_based(): void
    {
        $this->assertConfigurationIsValid(
            [['zones' => ['WEST-COAST' => ['name' => 'West Coast', 'provinces' => ['US-CA', 'US-OR', 'US-WA']]]]],
            'zones'
        );
    }

    /**
     * @test
     */
    public function zones_can_be_defined_as_zone_based(): void
    {
        $this->assertConfigurationIsValid(
            [['zones' => ['AMERICA' => ['name' => 'America', 'zones' => ['NORTH-AMERICA', 'SOUTH-AMERICA']]]]],
            'zones'
        );
    }

    /**
     * @test
     */
    public function zone_can_be_defined_with_exactly_one_kind_of_members(): void
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
    protected function getConfiguration(): GeographicalFixture
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
