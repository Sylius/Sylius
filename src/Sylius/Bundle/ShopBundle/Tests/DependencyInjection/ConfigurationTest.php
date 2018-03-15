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

namespace Sylius\Bundle\ShopBundle\Tests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ShopBundle\DependencyInjection\Configuration;

final class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function it_has_default_configuration_for_locale_switching_strategy(): void
    {
        $this->assertProcessedConfigurationEquals(
            [[]],
            ['locale_switcher' => 'url'],
            'locale_switcher'
        );
    }

    /**
     * @test
     */
    public function its_locale_switching_strategy_can_only_be_url_and_storage(): void
    {
        $this->assertConfigurationIsValid([[
            'locale_switcher' => 'url',
        ]]);

        $this->assertConfigurationIsValid([[
            'locale_switcher' => 'storage',
        ]]);

        $this->assertConfigurationIsInvalid([[
            'locale_switcher' => 'native',
        ]]);

        $this->assertConfigurationIsInvalid([[
            'locale_switcher' => true,
        ]]);

        $this->assertConfigurationIsInvalid([[
            'locale_switcher' => [],
        ]]);

        $this->assertConfigurationIsInvalid([[
            'locale_switcher' => 12,
        ]]);
    }

    /**
     * @test
     */
    public function it_has_default_configuration_for_firewall_context_name_node(): void
    {
        $this->assertProcessedConfigurationEquals(
            [[]],
            ['firewall_context_name' => 'shop'],
            'firewall_context_name'
        );
    }

    /**
     * @test
     */
    public function it_has_default_configuration_for_checkout_resolver_node(): void
    {
        $this->assertProcessedConfigurationEquals(
            [[]],
            ['checkout_resolver' => [
                    'enabled' => true,
                    'pattern' => '/checkout/.+',
                    'route_map' => [],
            ]],
            'checkout_resolver'
        );
    }

    /**
     * @test
     */
    public function its_checkout_resolver_pattern_accept_only_string_value(): void
    {
        $this->assertConfigurationIsInvalid([[
            'checkout_resolver' => [
                'pattern' => 1,
            ],
        ]]);

        $this->assertConfigurationIsInvalid([[
            'checkout_resolver' => [
                'pattern' => true,
            ],
        ]]);

        $this->assertConfigurationIsInvalid([[
            'checkout_resolver' => [
                'pattern' => 1.24,
            ],
        ]]);

        $this->assertConfigurationIsInvalid([[
            'checkout_resolver' => [
                'pattern' => [],
            ],
        ]]);
    }

    /**
     * @test
     */
    public function its_checkout_route_map_it_is_configurable(): void
    {
        $this->assertProcessedConfigurationEquals([
            ['checkout_resolver' => [
                'route_map' => [
                    'addressed' => [
                        'route' => 'sylius_shop_checkout_select_shipping',
                    ],
                ],
            ]], ],
            ['checkout_resolver' => [
                'enabled' => true,
                'pattern' => '/checkout/.+',
                'route_map' => [
                    'addressed' => [
                        'route' => 'sylius_shop_checkout_select_shipping',
                    ],
                ],
            ]],
            'checkout_resolver'
        );
    }

    /**
     * @test
     */
    public function its_checkout_route_map_route_cannot_be_empty(): void
    {
        $this->assertConfigurationIsInvalid([[
            'checkout_resolver' => [
                'route_map' => [
                    'addressed' => [],
                ],
            ],
        ]]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration(): Configuration
    {
        return new Configuration();
    }
}
