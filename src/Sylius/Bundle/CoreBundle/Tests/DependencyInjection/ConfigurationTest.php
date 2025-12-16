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

namespace Sylius\Bundle\CoreBundle\Tests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    public function test_it_configures_batch_size_to_100_by_default(): void
    {
        $this->assertProcessedConfigurationEquals(
            [[]],
            ['catalog_promotions' => ['batch_size' => 100]],
            'catalog_promotions',
        );
    }

    public function test_it_allows_for_assigning_integer_as_batch_size(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['catalog_promotions' => ['batch_size' => 200]]],
            ['catalog_promotions' => ['batch_size' => 200]],
            'catalog_promotions',
        );
    }

    public function test_it_enables_order_by_identifier_by_default(): void
    {
        $this->assertProcessedConfigurationEquals(
            [[]],
            ['order_by_identifier' => true],
            'order_by_identifier',
        );
    }

    public function test_it_allows_to_enable_order_by_identifier(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['order_by_identifier' => true]],
            ['order_by_identifier' => true],
            'order_by_identifier',
        );
    }

    public function test_it_allows_to_disable_order_by_identifier(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['order_by_identifier' => false]],
            ['order_by_identifier' => false],
            'order_by_identifier',
        );
    }

    public function test_it_allows_to_configure_orders_statistics_intervals_map(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                [
                    'orders_statistics' => [
                        'intervals_map' => [
                            'day' => [
                                'interval' => 'P1D',
                                'period_format' => 'Y-m-d',
                            ],
                            'month' => [
                                'interval' => 'P1M',
                                'period_format' => 'Y-m',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'orders_statistics' => [
                    'intervals_map' => [
                        'day' => [
                            'interval' => 'P1D',
                            'period_format' => 'Y-m-d',
                        ],
                        'month' => [
                            'interval' => 'P1M',
                            'period_format' => 'Y-m',
                        ],
                    ],
                ],
            ],
            'orders_statistics',
        );
    }

    public function test_it_throws_an_exception_if_orders_statistics_intervals_map_interval_is_empty(): void
    {
        $this->assertConfigurationIsInvalid(
            [['orders_statistics' => ['intervals_map' => ['day' => ['interval' => '', 'period_format' => 'Y-m-d']]]]],
            'The path "sylius_core.orders_statistics.intervals_map.day.interval" cannot contain an empty value, but got "".',
        );
    }

    public function test_it_throws_an_exception_if_orders_statistics_intervals_map_interval_is_invalid(): void
    {
        $this->assertConfigurationIsInvalid(
            [['orders_statistics' => ['intervals_map' => ['day' => ['interval' => 'invalid', 'period_format' => 'Y-m-d']]]]],
            'Invalid format for interval ""invalid"". Expected a string compatible with DateInterval.',
        );
    }

    public function test_it_throws_an_exception_if_orders_statistics_intervals_map_period_format_is_empty(): void
    {
        $this->assertConfigurationIsInvalid(
            [['orders_statistics' => ['intervals_map' => ['day' => ['interval' => 'P1D', 'period_format' => '']]]]],
            'The path "sylius_core.orders_statistics.intervals_map.day.period_format" cannot contain an empty value, but got "".',
        );
    }

    public function test_it_allows_to_configure_a_default_state_machine_adapter(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                [
                    'state_machine' => [
                        'default_adapter' => 'symfony_workflow',
                    ],
                ],
            ],
            [
                'state_machine' => [
                    'default_adapter' => 'symfony_workflow',
                    'graphs_to_adapters_mapping' => [],
                ],
            ],
            'state_machine',
        );
    }

    public function test_it_allows_to_configure_the_state_machines_adapters_mapping(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                [
                    'state_machine' => [
                        'graphs_to_adapters_mapping' => [
                            'order' => 'symfony_workflow',
                            'payment' => 'winzou_state_machine',
                        ],
                    ],
                ],
            ],
            [
                'state_machine' => [
                    'default_adapter' => 'winzou_state_machine',
                    'graphs_to_adapters_mapping' => [
                        'order' => 'symfony_workflow',
                        'payment' => 'winzou_state_machine',
                    ],
                ],
            ],
            'state_machine',
        );
    }

    public function test_it_has_a_set_default_order_token_length(): void
    {
        $this->assertProcessedConfigurationEquals(
            [[]],
            ['order_token_length' => 64],
            'order_token_length',
        );
    }

    public function test_it_allows_changing_the_order_token_length(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['order_token_length' => 128]],
            ['order_token_length' => 128],
            'order_token_length',
        );
    }

    public function test_it_throws_exception_when_order_token_length_is_invalid(): void
    {
        $this->assertConfigurationIsInvalid([['order_token_length' => 'string']]);
        $this->assertConfigurationIsInvalid(
            [['order_token_length' => 0]],
            '/Should be greater than or equal to 1$/',
            true,
        );
        $this->assertConfigurationIsInvalid(
            [['order_token_length' => 256]],
            '/Should be less than or equal to 255$/',
            true,
        );
    }

    public function test_it_throws_an_exception_if_value_other_then_integer_is_declared_as_batch_size(): void
    {
        $this->assertConfigurationIsInvalid([['catalog_promotions' => ['batch_size' => 'rep']]]);

        $this->assertConfigurationIsInvalid([['catalog_promotions' => ['batch_size' => 10.1]]]);
    }

    public function test_it_throws_an_exception_if_value_of_batch_size_is_lower_then_1(): void
    {
        $this->assertConfigurationIsInvalid(
            [['catalog_promotions' => ['batch_size' => -1]]],
            'Expected value bigger than 0, but got -1.',
        );

        $this->assertConfigurationIsInvalid(
            [['catalog_promotions' => ['batch_size' => 0]]],
            ' Expected value bigger than 0, but got 0.',
        );
    }

    public function test_it_does_not_autoconfigure_with_attributes_by_default(): void
    {
        $this->assertProcessedConfigurationEquals(
            [[]],
            ['autoconfigure_with_attributes' => false],
            'autoconfigure_with_attributes',
        );
    }

    public function test_it_allows_to_enable_autoconfiguring_with_attributes(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['autoconfigure_with_attributes' => true]],
            ['autoconfigure_with_attributes' => true],
            'autoconfigure_with_attributes',
        );
    }

    public function test_it_sets_default_allowed_images_mime_types(): void
    {
        $this->assertProcessedConfigurationEquals(
            [[]],
            ['allowed_images_mime_types' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp']],
            'allowed_images_mime_types',
        );
    }

    public function test_it_allows_setting_custom_allowed_images_mime_types(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['allowed_images_mime_types' => ['image/svg+xml', 'image/bmp']]],
            ['allowed_images_mime_types' => ['image/svg+xml', 'image/bmp']],
            'allowed_images_mime_types',
        );
    }

    public function test_it_sets_default_telemetry_configuration(): void
    {
        $this->assertProcessedConfigurationEquals(
            [[]],
            [
                'telemetry' => [
                    'enabled' => true,
                    'salt' => null,
                    'business' => true,
                    'technical' => true,
                    'plugins' => true,
                    'url' => 'https://prism.sylius.com/telemetry',
                ],
            ],
            'telemetry',
        );
    }

    public function test_it_allows_overriding_telemetry_configuration(): void
    {
        $this->assertProcessedConfigurationEquals(
            [[
                'telemetry' => [
                    'enabled' => false,
                    'salt' => 'custom-salt',
                ],
            ]],
            [
                'telemetry' => [
                    'enabled' => false,
                    'salt' => 'custom-salt',
                    'business' => true,
                    'technical' => true,
                    'plugins' => true,
                    'url' => 'https://prism.sylius.com/telemetry',
                ],
            ],
            'telemetry',
        );
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration();
    }
}
