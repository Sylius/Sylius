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

namespace Tests\Sylius\Abstraction\StateMachine\Functional\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Sylius\Abstraction\StateMachine\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /** @test */
    public function it_allows_to_configure_a_default_state_machine_adapter(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                [
                    'default_adapter' => 'symfony_workflow',
                ],
            ],
            [
                'default_adapter' => 'symfony_workflow',
                'graphs_to_adapters_mapping' => [],
            ],
        );
    }

    /** @test */
    public function it_allows_to_configure_the_state_machines_adapters_mapping(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                [
                    'graphs_to_adapters_mapping' => [
                        'order' => 'symfony_workflow',
                        'payment' => 'winzou_state_machine',
                    ],
                ],
            ],
            [
                'default_adapter' => 'winzou_state_machine',
                'graphs_to_adapters_mapping' => [
                    'order' => 'symfony_workflow',
                    'payment' => 'winzou_state_machine',
                ],
            ],
        );
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration();
    }
}
