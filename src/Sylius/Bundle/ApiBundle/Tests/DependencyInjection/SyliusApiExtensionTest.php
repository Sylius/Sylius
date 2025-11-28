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
use Sylius\Bundle\ApiBundle\DependencyInjection\SyliusApiExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SyliusApiExtensionTest extends TestCase
{
    private SyliusApiExtension $extension;

    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new SyliusApiExtension();
        $this->container = new ContainerBuilder();
        $this->container->setParameter('kernel.bundles_metadata', [
            'SyliusApiBundle' => [
                'path' => __DIR__ . '/../../',
            ],
        ]);
    }

    public function testSetsEnabledParameter(): void
    {
        $this->extension->load([['enabled' => true]], $this->container);

        $this->assertTrue($this->container->getParameter('sylius_api.enabled'));
    }

    public function testSetsAdminEndpointParameter(): void
    {
        $this->extension->load([['endpoints' => ['admin_enabled' => false]]], $this->container);

        $this->assertFalse($this->container->getParameter('sylius_api.endpoints.admin_enabled'));
    }

    public function testSetsShopEndpointParameter(): void
    {
        $this->extension->load([['endpoints' => ['shop_enabled' => false]]], $this->container);

        $this->assertFalse($this->container->getParameter('sylius_api.endpoints.shop_enabled'));
    }

    public function testSetsBothEndpointParameters(): void
    {
        $this->extension->load([[
            'endpoints' => [
                'admin_enabled' => false,
                'shop_enabled' => true,
            ],
        ]], $this->container);

        $this->assertFalse($this->container->getParameter('sylius_api.endpoints.admin_enabled'));
        $this->assertTrue($this->container->getParameter('sylius_api.endpoints.shop_enabled'));
    }

    public function testPrependsApiPlatformMappingWithBothEndpointsEnabled(): void
    {
        $configs = [[
            'endpoints' => [
                'admin_enabled' => true,
                'shop_enabled' => true,
            ],
        ]];

        $this->container->prependExtensionConfig('sylius_api', $configs[0]);
        $this->extension->prepend($this->container);

        $prependedConfig = $this->container->getExtensionConfig('api_platform');

        $this->assertNotEmpty($prependedConfig);
        $this->assertArrayHasKey('mapping', $prependedConfig[0]);
        $this->assertArrayHasKey('paths', $prependedConfig[0]['mapping']);
        $this->assertCount(2, $prependedConfig[0]['mapping']['paths']);
        $this->assertStringEndsWith('/Resources/config/api_platform/resources/admin', $prependedConfig[0]['mapping']['paths'][0]);
        $this->assertStringEndsWith('/Resources/config/api_platform/resources/shop', $prependedConfig[0]['mapping']['paths'][1]);
    }

    public function testPrependsApiPlatformMappingWithOnlyAdminEnabled(): void
    {
        $configs = [[
            'endpoints' => [
                'admin_enabled' => true,
                'shop_enabled' => false,
            ],
        ]];

        $this->container->prependExtensionConfig('sylius_api', $configs[0]);
        $this->extension->prepend($this->container);

        $prependedConfig = $this->container->getExtensionConfig('api_platform');

        $this->assertNotEmpty($prependedConfig);
        $this->assertArrayHasKey('mapping', $prependedConfig[0]);
        $this->assertArrayHasKey('paths', $prependedConfig[0]['mapping']);
        $this->assertCount(1, $prependedConfig[0]['mapping']['paths']);
        $this->assertStringEndsWith('/Resources/config/api_platform/resources/admin', $prependedConfig[0]['mapping']['paths'][0]);
    }

    public function testPrependsApiPlatformMappingWithOnlyShopEnabled(): void
    {
        $configs = [[
            'endpoints' => [
                'admin_enabled' => false,
                'shop_enabled' => true,
            ],
        ]];

        $this->container->prependExtensionConfig('sylius_api', $configs[0]);
        $this->extension->prepend($this->container);

        $prependedConfig = $this->container->getExtensionConfig('api_platform');

        $this->assertNotEmpty($prependedConfig);
        $this->assertArrayHasKey('mapping', $prependedConfig[0]);
        $this->assertArrayHasKey('paths', $prependedConfig[0]['mapping']);
        $this->assertCount(1, $prependedConfig[0]['mapping']['paths']);
        $this->assertStringEndsWith('/Resources/config/api_platform/resources/shop', $prependedConfig[0]['mapping']['paths'][0]);
    }

    public function testPrependsApiPlatformMappingWithBothEndpointsDisabled(): void
    {
        $configs = [[
            'endpoints' => [
                'admin_enabled' => false,
                'shop_enabled' => false,
            ],
        ]];

        $this->container->prependExtensionConfig('sylius_api', $configs[0]);
        $this->extension->prepend($this->container);

        $prependedConfig = $this->container->getExtensionConfig('api_platform');

        $this->assertEmpty($prependedConfig);
    }
}
