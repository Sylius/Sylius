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

namespace Sylius\Bundle\ThemeBundle\Tests\Configuration;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Sylius\Bundle\ThemeBundle\Configuration\ThemeConfiguration;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class ThemeConfigurationTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function it_requires_only_name(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                ['name' => 'example/sylius-theme'],
            ],
            ['name' => 'example/sylius-theme'],
            'name'
        );
    }

    /**
     * @test
     */
    public function its_name_is_required_and_cannot_be_empty(): void
    {
        $this->assertPartialConfigurationIsInvalid(
            [
                [/* no name defined */],
            ],
            'name'
        );

        $this->assertPartialConfigurationIsInvalid(
            [
                ['name' => ''],
            ],
            'name'
        );
    }

    /**
     * @test
     */
    public function its_title_is_optional_but_cannot_be_empty(): void
    {
        $this->assertPartialConfigurationIsInvalid(
            [
                ['title' => ''],
            ],
            'title'
        );

        $this->assertConfigurationIsValid(
            [
                ['title' => 'Lorem ipsum'],
            ],
            'title'
        );
    }

    /**
     * @test
     */
    public function its_description_is_optional_but_cannot_be_empty(): void
    {
        $this->assertPartialConfigurationIsInvalid(
            [
                ['description' => ''],
            ],
            'description'
        );

        $this->assertConfigurationIsValid(
            [
                ['description' => 'Lorem ipsum dolor sit amet'],
            ],
            'description'
        );
    }

    /**
     * @test
     */
    public function its_path_is_optional_but_cannot_be_empty(): void
    {
        $this->assertPartialConfigurationIsInvalid(
            [
                ['path' => ''],
            ],
            'path'
        );

        $this->assertConfigurationIsValid(
            [
                ['path' => '/theme/path'],
            ],
            'path'
        );
    }

    /**
     * @test
     */
    public function its_authors_are_optional(): void
    {
        $this->assertConfigurationIsValid(
            [
                [/* no authors defined */],
            ],
            'authors'
        );
    }

    /**
     * @test
     */
    public function its_author_can_have_only_name_email_homepage_and_role_properties(): void
    {
        $this->assertConfigurationIsValid(
            [
                ['authors' => [['name' => 'Kamil Kokot']]],
            ],
            'authors'
        );

        $this->assertConfigurationIsValid(
            [
                ['authors' => [['email' => 'kamil@kokot.me']]],
            ],
            'authors'
        );

        $this->assertConfigurationIsValid(
            [
                ['authors' => [['homepage' => 'http://kamil.kokot.me']]],
            ],
            'authors'
        );

        $this->assertConfigurationIsValid(
            [
                ['authors' => [['role' => 'Developer']]],
            ],
            'authors'
        );

        $this->assertPartialConfigurationIsInvalid(
            [
                ['authors' => [['undefined' => '42']]],
            ],
            'authors'
        );
    }

    /**
     * @test
     */
    public function its_author_must_have_at_least_one_property(): void
    {
        $this->assertPartialConfigurationIsInvalid(
            [
                ['authors' => [[/* empty author */]]],
            ],
            'authors',
            'Author cannot be empty'
        );
    }

    /**
     * @test
     */
    public function its_authors_replaces_other_authors_defined_elsewhere(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                ['authors' => [['name' => 'Kamil Kokot']]],
                ['authors' => [['name' => 'Krzysztof Krawczyk']]],
            ],
            ['authors' => [['name' => 'Krzysztof Krawczyk']]],
            'authors'
        );
    }

    /**
     * @test
     */
    public function it_ignores_undefined_root_level_fields(): void
    {
        $this->assertConfigurationIsValid(
            [
                ['name' => 'example/sylius-theme', 'undefined_variable' => '42'],
            ]
        );
    }

    /**
     * @test
     */
    public function its_parents_are_optional_but_has_to_have_at_least_one_element(): void
    {
        $this->assertConfigurationIsValid(
            [
                [],
            ],
            'parents'
        );

        $this->assertPartialConfigurationIsInvalid(
            [
                ['parents' => [/* no elements */]],
            ],
            'parents'
        );
    }

    /**
     * @test
     */
    public function its_parent_is_strings(): void
    {
        $this->assertConfigurationIsValid(
            [
                ['parents' => ['example/parent-theme', 'example/parent-theme-2']],
            ],
            'parents'
        );
    }

    /**
     * @test
     */
    public function its_parent_cannot_be_empty(): void
    {
        $this->assertPartialConfigurationIsInvalid(
            [
                ['parents' => ['']],
            ],
            'parents'
        );
    }

    /**
     * @test
     */
    public function its_parents_replaces_other_parents_defined_elsewhere(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                ['parents' => ['example/first-theme']],
                ['parents' => ['example/second-theme']],
            ],
            ['parents' => ['example/second-theme']],
            'parents'
        );
    }

    /**
     * @test
     */
    public function its_screenshots_are_strings(): void
    {
        $this->assertConfigurationIsValid(
            [
                ['screenshots' => ['screenshot/krzysztof-krawczyk.jpg', 'screenshot/ryszard-rynkowski.jpg']],
            ],
            'screenshots'
        );
    }

    /**
     * @test
     */
    public function its_screenshots_are_optional(): void
    {
        $this->assertConfigurationIsValid(
            [
                [/* no screenshots defined */],
            ],
            'screenshots'
        );
    }

    /**
     * @test
     */
    public function its_screenshots_must_have_at_least_one_element(): void
    {
        $this->assertPartialConfigurationIsInvalid(
            [
                ['screenshots' => [/* no elements */]],
            ],
            'screenshots'
        );
    }

    /**
     * @test
     */
    public function its_screenshots_cannot_be_empty(): void
    {
        $this->assertPartialConfigurationIsInvalid(
            [
                ['screenshots' => ['']],
            ],
            'screenshots'
        );
    }

    /**
     * @test
     */
    public function its_screenshots_replaces_other_screenshots_defined_elsewhere(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                ['screenshots' => ['screenshot/zbigniew-holdys.jpg']],
                ['screenshots' => ['screenshot/maryla-rodowicz.jpg']],
            ],
            ['screenshots' => [['path' => 'screenshot/maryla-rodowicz.jpg']]],
            'screenshots'
        );
    }

    /**
     * @test
     */
    public function its_screenshots_are_an_array(): void
    {
        $this->assertConfigurationIsValid(
            [
                ['screenshots' => [['path' => 'screenshot/rick-astley.jpg']]],
            ],
            'screenshots'
        );
    }

    /**
     * @test
     */
    public function its_screenshots_must_have_a_path(): void
    {
        $this->assertPartialConfigurationIsInvalid(
            [
                ['screenshots' => [['title' => 'Candy shop']]],
            ],
            'screenshots'
        );
    }

    /**
     * @test
     */
    public function its_screenshots_have_optional_title_and_description(): void
    {
        $this->assertConfigurationIsValid(
            [
                ['screenshots' => [[
                    'path' => 'screenshot/rick-astley.jpg',
                    'title' => 'Rick Astley',
                    'description' => 'He\'ll never gonna give you up or let you down',
                ]]],
            ],
            'screenshots'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration(): ConfigurationInterface
    {
        return new ThemeConfiguration();
    }
}
