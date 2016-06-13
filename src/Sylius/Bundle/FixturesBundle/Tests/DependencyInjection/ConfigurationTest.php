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
    public function it_allows_to_register_suite_with_fixture()
    {
        $this->assertConfigurationIsValid(
            [['suites' => ['suite' => ['fixtures' => ['fixture' => null]]]]],
            'suites.*.fixtures'
        );
    }

    /**
     * @test
     */
    public function it_allows_to_register_multiple_suites()
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
    public function it_allows_to_add_a_new_suite()
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
                'first_suite' => ['fixtures' => ['fixture' => ['options' => [[]], 'priority' => 0]]],
                'second_suite' => ['fixtures' => ['fixture' => ['options' => [[]], 'priority' => 0]]],
            ]],
            'suites.*.fixtures'
        );
    }

    /**
     * @test
     */
    public function it_allows_to_register_multiple_fixtures_for_one_suite()
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
    public function it_allows_to_unset_a_fixture()
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
                'first_fixture' => ['options' => [[]], 'priority' => 0],
            ]]]],
            'suites.*.fixtures'
        );
    }

    /**
     * @test
     */
    public function it_allows_to_add_a_new_fixture_to_a_suite()
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
                'first_fixture' => ['options' => [[]], 'priority' => 0],
                'second_fixture' => ['options' => [[]], 'priority' => 0],
            ]]]],
            'suites.*.fixtures'
        );
    }

    /**
     * @test
     */
    public function it_collects_all_fixtures_options()
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
                'fixture' => ['options' => [['option' => 4], ['option' => 2]], 'priority' => 0],
            ]]]],
            'suites.*.fixtures'
        );
    }

    /**
     * @test
     */
    public function it_does_not_replace_fixture_options_if_not_defined_explicitly()
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
                'fixture' => ['options' => [['option' => 4]], 'priority' => 0],
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
