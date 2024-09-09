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

namespace Sylius\Bundle\UiBundle\Tests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\UiBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /** @test */
    public function empty_configuration_triggers_no_errors(): void
    {
        $this->assertConfigurationIsValid([[]], 'events');
        $this->assertConfigurationIsValid([['events' => []]], 'events');
    }

    /** @test */
    public function empty_events_configuration_triggers_no_errors(): void
    {
        $this->assertConfigurationIsValid([['events' => ['event_name' => []]]], 'events');
        $this->assertConfigurationIsValid([['events' => ['event_name' => ['blocks' => []]]]], 'events');
    }

    /** @test */
    public function multiple_events_might_be_configured(): void
    {
        $this->assertConfigurationIsValid([['events' => ['first_event' => [], 'second_event' => []]]], 'events');
    }

    /** @test */
    public function consecutive_event_configuration_are_merged(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                ['events' => ['first_event' => []]],
                ['events' => ['second_event' => []]],
            ],
            ['events' => ['first_event' => ['blocks' => []], 'second_event' => ['blocks' => []]]],
            'events',
        );
    }

    /** @test */
    public function event_configuration_has_block_configuration(): void
    {
        $this->assertConfigurationIsValid(
            [['events' => ['event_name' => ['blocks' => ['block_name' => ['template' => 'block.html.twig']]]]]],
            'events',
        );
    }

    /** @test */
    public function multiple_blocks_can_be_configured_for_an_event(): void
    {
        $this->assertConfigurationIsValid(
            [['events' => ['event_name' => ['blocks' => [
                'first_block' => ['template' => 'block.html.twig'],
                'second_block' => ['template' => 'block.html.twig'],
            ]]]]],
            'events',
        );
    }

    /** @test */
    public function block_has_default_priority_set_to_zero(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['events' => ['event_name' => ['blocks' => ['block_name' => []]]]]],
            ['events' => ['event_name' => ['blocks' => ['block_name' => ['priority' => 0]]]]],
            'events.*.blocks.*.priority',
        );
    }

    /** @test */
    public function block_priority_is_set_within_its_configuration(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['events' => ['event_name' => ['blocks' => ['block_name' => ['priority' => 100]]]]]],
            ['events' => ['event_name' => ['blocks' => ['block_name' => ['priority' => 100]]]]],
            'events.*.blocks.*.priority',
        );
    }

    /** @test */
    public function block_is_null_by_default(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['events' => ['event_name' => ['blocks' => ['block_name' => []]]]]],
            ['events' => ['event_name' => ['blocks' => ['block_name' => ['enabled' => null]]]]],
            'events.*.blocks.*.enabled',
        );
    }

    /** @test */
    public function block_can_be_disabled_within_its_configuration(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['events' => ['event_name' => ['blocks' => ['block_name' => ['enabled' => false]]]]]],
            ['events' => ['event_name' => ['blocks' => ['block_name' => ['enabled' => false]]]]],
            'events.*.blocks.*.enabled',
        );
    }

    /** @test */
    public function block_configuration_can_be_shortened_to_template_string_only(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['events' => ['event_name' => ['blocks' => ['block_name' => 'template.html.twig']]]]],
            ['events' => ['event_name' => ['blocks' => ['block_name' => ['template' => 'template.html.twig']]]]],
            'events.*.blocks.*.template',
        );
    }

    /** @test */
    public function consecutive_block_configurations_can_change_the_template(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                ['events' => ['event_name' => ['blocks' => ['block_name' => 'template.html.twig']]]],
                ['events' => ['event_name' => ['blocks' => ['block_name' => ['template' => 'another_template.html.twig']]]]],
            ],
            ['events' => ['event_name' => ['blocks' => ['block_name' => ['template' => 'another_template.html.twig']]]]],
            'events.*.blocks.*.template',
        );
    }

    /** @test */
    public function consecutive_block_configurations_can_change_the_priority(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                ['events' => ['event_name' => ['blocks' => ['block_name' => ['priority' => 42]]]]],
                ['events' => ['event_name' => ['blocks' => ['block_name' => ['priority' => 13]]]]],
            ],
            ['events' => ['event_name' => ['blocks' => ['block_name' => ['priority' => 13]]]]],
            'events.*.blocks.*.priority',
        );
    }

    /** @test */
    public function consecutive_block_configurations_can_disable_it(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                ['events' => ['event_name' => ['blocks' => ['block_name' => ['enabled' => true]]]]],
                ['events' => ['event_name' => ['blocks' => ['block_name' => ['enabled' => false]]]]],
            ],
            ['events' => ['event_name' => ['blocks' => ['block_name' => ['enabled' => false]]]]],
            'events.*.blocks.*.enabled',
        );
    }

    /** @test */
    public function consecutive_block_configurations_are_merged(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                ['events' => ['event_name' => ['blocks' => ['first_block' => 'first.html.twig']]]],
                ['events' => ['event_name' => ['blocks' => ['second_block' => 'second.html.twig']]]],
            ],
            ['events' => ['event_name' => ['blocks' => [
                'first_block' => ['template' => 'first.html.twig'],
                'second_block' => ['template' => 'second.html.twig'],
            ]]]],
            'events.*.blocks.*.template',
        );
    }

    /** @test */
    public function context_can_be_passed_to_the_block(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['events' => ['event_name' => ['blocks' => ['block_name' => ['context' => ['foo' => 'bar']]]]]]],
            ['events' => ['event_name' => ['blocks' => ['block_name' => ['context' => ['foo' => 'bar']]]]]],
            'events.*.blocks.*.context',
        );
    }

    /** @test */
    public function consecutive_block_context_configuration_is_shallowly_merged(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                ['events' => ['event_name' => ['blocks' => ['block_name' => ['context' => ['foo' => 'bar']]]]]],
                ['events' => ['event_name' => ['blocks' => ['block_name' => ['context' => ['bar' => 'baz']]]]]],
            ],
            ['events' => ['event_name' => ['blocks' => ['block_name' => ['context' => ['foo' => 'bar', 'bar' => 'baz']]]]]],
            'events.*.blocks.*.context',
        );

        $this->assertProcessedConfigurationEquals(
            [
                ['events' => ['event_name' => ['blocks' => ['block_name' => ['context' => ['foo' => ['bar']]]]]]],
                ['events' => ['event_name' => ['blocks' => ['block_name' => ['context' => ['foo' => ['baz']]]]]]],
            ],
            ['events' => ['event_name' => ['blocks' => ['block_name' => ['context' => ['foo' => ['baz']]]]]]],
            'events.*.blocks.*.context',
        );
    }

    /** @test */
    public function component_block_configuration_can_be_shortened_to_template_string_only(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                [
                    'events' => [
                        'event_name' => [
                            'blocks' => [
                                'block_name' => [
                                    'component' => 'component_name',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'events' => [
                    'event_name' => [
                        'blocks' => [
                            'block_name' => [
                                'component' => [
                                    'name' => 'component_name',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'events.*.blocks.*.component',
        );
    }

    /** @test */
    public function component_block_configuration_can_be_defined_with_inputs(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                [
                    'events' => [
                        'event_name' => [
                            'blocks' => [
                                'block_name' => [
                                    'component' => [
                                        'name' => 'component_name',
                                        'inputs' => [
                                            'foo' => 'bar',
                                            'bar' => 'baz',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'events' => [
                    'event_name' => [
                        'blocks' => [
                            'block_name' => [
                                'component' => [
                                    'name' => 'component_name',
                                    'inputs' => [
                                        'foo' => 'bar',
                                        'bar' => 'baz',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'events.*.blocks.*.component',
        );
    }

    /** @test */
    public function template_and_component_cannot_be_defined_at_the_same_time(): void
    {
        $this->assertPartialConfigurationIsInvalid(
            [
                [
                    'events' => [
                        'event_name' => [
                            'blocks' => [
                                'block_name' => [
                                    'template' => 'template.html.twig',
                                    'component' => [
                                        'name' => 'component_name',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'events.*.blocks.*.template',
        );
    }

    /** @test */
    public function it_allows_empty_twig_ux_configuration(): void
    {
        $this->assertConfigurationIsValid([['twig_ux' => []]], 'twig_ux');
    }

    /** @test */
    public function it_allows_to_configure_anonymous_component_template_prefixes(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                ['twig_ux' => ['anonymous_component_template_prefixes' => ['sylius_ui' => '@SyliusUi']]],
            ],
            ['twig_ux' => ['anonymous_component_template_prefixes' => ['sylius_ui' => '@SyliusUi']]],
            'twig_ux.anonymous_component_template_prefixes',
        );
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration();
    }
}
