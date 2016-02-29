<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
    public function it_uses_app_themes_filesystem_as_the_default_source()
    {
        $this->assertProcessedConfigurationEquals(
           [
               [],
           ],
           ['sources' => ['filesystem' => ['locations' => ['%kernel.root_dir%/themes', '%kernel.root_dir%/../vendor/sylius/themes']]]],
            'sources'
       );
    }

    /**
     * @test
     */
    public function it_does_not_add_default_theme_location_if_there_are_some_defined_by_user()
    {
        $this->assertProcessedConfigurationEquals(
            [
                ['sources' => ['filesystem' => ['locations' => ['/custom/path', '/custom/path2']]]],
            ],
            ['sources' => ['filesystem' => ['locations' => ['/custom/path', '/custom/path2']]]],
            'sources.filesystem'
        );
    }

    /**
     * @test
     */
    public function it_uses_the_last_theme_locations_passed_and_rejects_the_other_ones()
    {
        $this->assertProcessedConfigurationEquals(
            [
                ['sources' => ['filesystem' => ['locations' => ['/custom/path', '/custom/path2']]]],
                ['sources' => ['filesystem' => ['locations' => ['/last/custom/path']]]],
            ],
            ['sources' => ['filesystem' => ['locations' => ['/last/custom/path']]]],
            'sources.filesystem'
        );
    }

    /**
     * @test
     */
    public function it_is_invalid_to_pass_a_string_as_theme_locations()
    {
        $this->assertPartialConfigurationIsInvalid(
            [
                ['locations' => '/string/not/array'],
            ],
            'sources.filesystem'
        );
    }

    /**
     * @test
     */
    public function it_throws_an_error_if_trying_to_set_theme_locations_to_an_empty_array()
    {
        $this->assertPartialConfigurationIsInvalid(
            [
                ['locations' => []],
            ],
            'sources.filesystem'
        );
    }

    /**
     * @test
     */
    public function it_has_default_context_service_set()
    {
        $this->assertProcessedConfigurationEquals(
            [
                [],
            ],
            ['context' => 'sylius.theme.context.settable'],
            'context'
        );
    }

    /**
     * @test
     */
    public function its_context_cannot_be_empty()
    {
        $this->assertPartialConfigurationIsInvalid(
            [
                ['']
            ],
            'context'
        );
    }

    /**
     * @test
     */
    public function its_context_can_be_overrided()
    {
        $this->assertProcessedConfigurationEquals(
            [
                ['context' => 'sylius.theme.context.custom'],
            ],
            ['context' => 'sylius.theme.context.custom'],
            'context'
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
