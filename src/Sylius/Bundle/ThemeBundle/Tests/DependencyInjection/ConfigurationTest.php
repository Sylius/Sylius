<?php

namespace Sylius\Bundle\ThemeBundle\Tests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Sylius\Bundle\ThemeBundle\DependencyInjection\Configuration;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function it_uses_app_themes_as_default_themes_location()
    {
        $this->assertProcessedConfigurationEquals(
            [
                [],
            ],
            ['locations' => ['%kernel.root_dir%/themes']]
        );
    }

    /**
     * @test
     */
    public function it_does_not_add_default_theme_location_if_there_are_some_defined_by_user()
    {
        $this->assertProcessedConfigurationEquals(
            [
                ['locations' => ['/custom/path', '/custom/path2']],
            ],
            ['locations' => ['/custom/path', '/custom/path2']]
        );
    }

    /**
     * @test
     */
    public function it_uses_the_last_theme_locations_passed_and_rejects_the_other_ones()
    {
        $this->assertProcessedConfigurationEquals(
            [
                ['locations' => ['/custom/path', '/custom/path2']],
                ['locations' => ['/last/custom/path']],
            ],
            ['locations' => ['/last/custom/path']]
        );
    }

    /**
     * @test
     */
    public function it_is_invalid_to_pass_a_string_as_theme_locations()
    {
        $this->assertConfigurationIsInvalid(
            [
                ['locations' => '/string/not/array'],
            ]
        );
    }

    /**
     * @test
     */
    public function it_throws_an_error_if_trying_to_set_theme_locations_to_an_empty_array()
    {
        $this->assertConfigurationIsInvalid(
            [
                ['locations' => []],
            ]
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
