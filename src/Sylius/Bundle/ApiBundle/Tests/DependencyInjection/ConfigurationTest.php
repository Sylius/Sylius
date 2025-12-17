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

namespace Tests\Sylius\Bundle\ApiBundle\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

final class ConfigurationTest extends TestCase
{
    private Configuration $configuration;

    private Processor $processor;

    protected function setUp(): void
    {
        $this->configuration = new Configuration();
        $this->processor = new Processor();
    }

    public function testDefaultConfiguration(): void
    {
        $processedConfig = $this->processor->processConfiguration(
            $this->configuration,
            [[]],
        );

        $this->assertFalse($processedConfig['enabled']);
        $this->assertTrue($processedConfig['endpoints']['admin_enabled']);
        $this->assertTrue($processedConfig['endpoints']['shop_enabled']);
    }

    public function testEnabledCanBeSetToTrue(): void
    {
        $processedConfig = $this->processor->processConfiguration(
            $this->configuration,
            [['enabled' => true]],
        );

        $this->assertTrue($processedConfig['enabled']);
    }

    public function testAdminEndpointsCanBeDisabled(): void
    {
        $processedConfig = $this->processor->processConfiguration(
            $this->configuration,
            [['endpoints' => ['admin_enabled' => false]]],
        );

        $this->assertFalse($processedConfig['endpoints']['admin_enabled']);
        $this->assertTrue($processedConfig['endpoints']['shop_enabled']);
    }

    public function testShopEndpointsCanBeDisabled(): void
    {
        $processedConfig = $this->processor->processConfiguration(
            $this->configuration,
            [['endpoints' => ['shop_enabled' => false]]],
        );

        $this->assertTrue($processedConfig['endpoints']['admin_enabled']);
        $this->assertFalse($processedConfig['endpoints']['shop_enabled']);
    }

    public function testBothEndpointsCanBeDisabled(): void
    {
        $processedConfig = $this->processor->processConfiguration(
            $this->configuration,
            [['endpoints' => ['admin_enabled' => false, 'shop_enabled' => false]]],
        );

        $this->assertFalse($processedConfig['endpoints']['admin_enabled']);
        $this->assertFalse($processedConfig['endpoints']['shop_enabled']);
    }

    public function testBothEndpointsCanBeEnabled(): void
    {
        $processedConfig = $this->processor->processConfiguration(
            $this->configuration,
            [['endpoints' => ['admin_enabled' => true, 'shop_enabled' => true]]],
        );

        $this->assertTrue($processedConfig['endpoints']['admin_enabled']);
        $this->assertTrue($processedConfig['endpoints']['shop_enabled']);
    }

    public function testEndpointsConfigurationWithDefaultsIfNotSet(): void
    {
        $processedConfig = $this->processor->processConfiguration(
            $this->configuration,
            [['enabled' => true]],
        );

        $this->assertArrayHasKey('endpoints', $processedConfig);
        $this->assertArrayHasKey('admin_enabled', $processedConfig['endpoints']);
        $this->assertArrayHasKey('shop_enabled', $processedConfig['endpoints']);
        $this->assertTrue($processedConfig['endpoints']['admin_enabled']);
        $this->assertTrue($processedConfig['endpoints']['shop_enabled']);
    }
}
