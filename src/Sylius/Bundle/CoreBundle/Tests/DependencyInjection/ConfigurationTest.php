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

    /** @test */
    public function it_configures_batch_size_to_100_by_default(): void
    {
        $this->assertProcessedConfigurationEquals(
            [[]],
            ['catalog_promotions' => ['batch_size' => 100]],
            'catalog_promotions',
        );
    }

    /** @test */
    public function it_allows_for_assigning_integer_as_batch_size(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['catalog_promotions' => ['batch_size' => 200]]],
            ['catalog_promotions' => ['batch_size' => 200]],
            'catalog_promotions',
        );
    }

    /** @test */
    public function it_enables_order_by_identifier_by_default(): void
    {
        $this->assertProcessedConfigurationEquals(
            [[]],
            ['order_by_identifier' => true],
            'order_by_identifier',
        );
    }

    /** @test */
    public function it_allows_to_enable_order_by_identifier(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['order_by_identifier' => true]],
            ['order_by_identifier' => true],
            'order_by_identifier',
        );
    }

    /** @test */
    public function it_allows_to_disable_order_by_identifier(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['order_by_identifier' => false]],
            ['order_by_identifier' => false],
            'order_by_identifier',
        );
    }

    /** @test */
    public function it_allows_to_configure_a_default_state_machine_adapter(): void
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

    /** @test */
    public function it_allows_to_configure_the_state_machines_adapters_mapping(): void
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

    /** @test */
    public function it_throws_an_exception_if_value_other_then_integer_is_declared_as_batch_size(): void
    {
        $this->assertConfigurationIsInvalid([['catalog_promotions' => ['batch_size' => 'rep']]]);

        $this->assertConfigurationIsInvalid([['catalog_promotions' => ['batch_size' => 10.1]]]);
    }

    /** @test */
    public function it_throws_an_exception_if_value_of_batch_size_is_lower_then_1(): void
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

    /** @test */
    public function it_does_not_autoconfigure_with_attributes_by_default(): void
    {
        $this->assertProcessedConfigurationEquals(
            [[]],
            ['autoconfigure_with_attributes' => false],
            'autoconfigure_with_attributes',
        );
    }

    /** @test */
    public function it_allows_to_enable_autoconfiguring_with_attributes(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['autoconfigure_with_attributes' => true]],
            ['autoconfigure_with_attributes' => true],
            'autoconfigure_with_attributes',
        );
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration();
    }
}
