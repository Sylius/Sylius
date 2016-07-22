<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\Tests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Sylius\Bundle\FixturesBundle\DependencyInjection\Configuration;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function suite_can_have_one_fixture()
    {
        $this->assertConfigurationIsValid(
            [['suites' => ['suite' => ['fixtures' => ['fixture' => null]]]]],
            'suites.*.fixtures'
        );
    }

    /**
     * @test
     */
    public function multiple_suites_are_allowed()
    {
        $this->assertConfigurationIsValid(
            [['suites' => [
                'first_suite' => ['fixtures' => ['fixture' => null]],
                'second_suite' => ['fixtures' => ['fixture' => null]],
            ]]],
            'suites.*.fixtures'
        );
    }

    /**
     * @test
     */
    public function consecutive_configurations_can_add_suites()
    {
        $this->assertProcessedConfigurationEquals(
            [
                ['suites' => [
                    'first_suite' => ['fixtures' => ['fixture' => null]],
                ]],
                ['suites' => [
                    'second_suite' => ['fixtures' => ['fixture' => null]],
                ]],
            ],
            ['suites' => [
                'first_suite' => ['fixtures' => ['fixture' => ['name' => 'fixture', 'options' => [[]], 'priority' => 0]]],
                'second_suite' => ['fixtures' => ['fixture' => ['name' => 'fixture', 'options' => [[]], 'priority' => 0]]],
            ]],
            'suites.*.fixtures'
        );
    }

    /**
     * @test
     */
    public function suite_can_have_multiple_fixtures()
    {
        $this->assertConfigurationIsValid(
            [['suites' => ['suite' => ['fixtures' => [
                'first_fixture' => null,
                'second_fixture' => null,
            ]]]]],
            'suites.*.fixtures'
        );
    }

    /**
     * @test
     */
    public function consecutive_configurations_can_remove_a_fixture_from_the_suite()
    {
        $this->assertProcessedConfigurationEquals(
            [
                ['suites' => ['suite' => ['fixtures' => [
                    'first_fixture' => null,
                    'second_fixture' => null,
                ]]]],
                ['suites' => ['suite' => ['fixtures' => [
                    'second_fixture' => false,
                ]]]],
            ],
            ['suites' => ['suite' => ['fixtures' => [
                'first_fixture' => ['name' => 'first_fixture', 'options' => [[]], 'priority' => 0],
            ]]]],
            'suites.*.fixtures'
        );
    }

    /**
     * @test
     */
    public function consecutive_configurations_can_add_fixtures_to_the_suite()
    {
        $this->assertProcessedConfigurationEquals(
            [
                ['suites' => ['suite' => ['fixtures' => [
                    'first_fixture' => null,
                ]]]],
                ['suites' => ['suite' => ['fixtures' => [
                    'second_fixture' => null,
                ]]]],
            ],
            ['suites' => ['suite' => ['fixtures' => [
                'first_fixture' => ['name' => 'first_fixture', 'options' => [[]], 'priority' => 0],
                'second_fixture' => ['name' => 'second_fixture', 'options' => [[]], 'priority' => 0],
            ]]]],
            'suites.*.fixtures'
        );
    }

    /**
     * @test
     */
    public function all_fixture_options_from_consecutive_configurations_are_collected()
    {
        $this->assertProcessedConfigurationEquals(
            [
                ['suites' => ['suite' => ['fixtures' => [
                    'fixture' => ['options' => ['option' => 4]],
                ]]]],
                ['suites' => ['suite' => ['fixtures' => [
                    'fixture' => ['options' => ['option' => 2]],
                ]]]],
            ],
            ['suites' => ['suite' => ['fixtures' => [
                'fixture' => ['name' => 'fixture', 'options' => [['option' => 4], ['option' => 2]], 'priority' => 0],
            ]]]],
            'suites.*.fixtures'
        );
    }

    /**
     * @test
     */
    public function fixture_options_are_not_replaced_implicitly()
    {
        $this->assertProcessedConfigurationEquals(
            [
                ['suites' => ['suite' => ['fixtures' => [
                    'fixture' => ['options' => ['option' => 4]],
                ]]]],
                ['suites' => ['suite' => ['fixtures' => [
                    'fixture' => null,
                ]]]],
            ],
            ['suites' => ['suite' => ['fixtures' => [
                'fixture' => ['name' => 'fixture', 'options' => [['option' => 4]], 'priority' => 0],
            ]]]],
            'suites.*.fixtures'
        );
    }

    /**
     * @test
     */
    public function fixtures_options_are_an_array()
    {
        $this->assertPartialConfigurationIsInvalid(
            [['suites' => ['suite' => ['fixtures' => ['fixture' => [
                'options' => 42,
            ]]]]]],
            'suites.*.fixtures'
        );

        $this->assertPartialConfigurationIsInvalid(
            [['suites' => ['suite' => ['fixtures' => ['fixture' => [
                'options' => 'string string',
            ]]]]]],
            'suites.*.fixtures'
        );
    }

    /**
     * @test
     */
    public function listeners_options_are_an_array()
    {
        $this->assertPartialConfigurationIsInvalid(
            [['suites' => ['suite' => ['listeners' => ['listener' => [
                'options' => 42,
            ]]]]]],
            'suites.*.listeners'
        );

        $this->assertPartialConfigurationIsInvalid(
            [['suites' => ['suite' => ['listeners' => ['listener' => [
                'options' => 'string string',
            ]]]]]],
            'suites.*.listeners'
        );
    }

    /**
     * @test
     */
    public function fixtures_options_may_contain_nested_arrays()
    {
        $this->assertProcessedConfigurationEquals(
            [['suites' => ['suite' => ['fixtures' => ['fixture' => [
                'options' => ['nested' => ['key' => 'value']],
            ]]]]]],
            ['suites' => ['suite' => ['fixtures' => ['fixture' => [
                'options' => [['nested' => ['key' => 'value']]],
                'name' => 'fixture', // FIXME: something is wrong inside the test library and it's not excluded
            ]]]]],
            'suites.*.fixtures.*.options'
        );
    }

    /**
     * @test
     */
    public function listeners_options_may_contain_nested_arrays()
    {
        $this->assertProcessedConfigurationEquals(
            [['suites' => ['suite' => ['listeners' => ['listener' => [
                'options' => ['nested' => ['key' => 'value']],
            ]]]]]],
            ['suites' => ['suite' => ['listeners' => ['listener' => [
                'options' => [['nested' => ['key' => 'value']]],
            ]]]]],
            'suites.*.listeners.*.options'
        );
    }

    /**
     * @test
     */
    public function fixtures_can_be_aliased_with_different_names()
    {
        $this->assertProcessedConfigurationEquals(
            [
                ['suites' => ['suite' => ['fixtures' => [
                    'admin_user' => ['name' => 'user', 'options' => ['admin' => true]],
                    'regular_user' => ['name' => 'user', 'options' => ['admin' => false]],
                ]]]],
            ],
            ['suites' => ['suite' => ['fixtures' => [
                'admin_user' => ['name' => 'user', 'options' => [['admin' => true]], 'priority' => 0],
                'regular_user' => ['name' => 'user', 'options' => [['admin' => false]], 'priority' => 0],
            ]]]],
            'suites.*.fixtures'
        );
    }

    /**
     * @test
     */
    public function consecutive_configurations_can_add_aliased_fixtures_to_the_suite()
    {
        $this->assertProcessedConfigurationEquals(
            [
                ['suites' => ['suite' => ['fixtures' => [
                    'admin_user' => ['name' => 'user', 'options' => ['admin' => true]],
                ]]]],
                ['suites' => ['suite' => ['fixtures' => [
                    'regular_user' => ['name' => 'user', 'options' => ['admin' => false]],
                ]]]],
            ],
            ['suites' => ['suite' => ['fixtures' => [
                'admin_user' => ['name' => 'user', 'options' => [['admin' => true]], 'priority' => 0],
                'regular_user' => ['name' => 'user', 'options' => [['admin' => false]], 'priority' => 0],
            ]]]],
            'suites.*.fixtures'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new Configuration();
    }
}
