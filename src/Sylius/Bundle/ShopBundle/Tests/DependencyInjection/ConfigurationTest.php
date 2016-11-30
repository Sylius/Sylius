<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShopBundle\Tests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Sylius\Bundle\ShopBundle\DependencyInjection\Configuration;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function it_has_default_configuration_for_checkout_resolver_node()
    {
        $this->assertProcessedConfigurationEquals(
            [
                [],
            ],
            [
                'checkout_resolver' => [
                    'enabled' => true,
                    'pattern' => '/checkout/.+',
                    'route_map' => [],
                ]
            ]
        );
    }

    /**
     * @test
     */
    public function its_checkout_resolver_pattern_accept_only_string_value()
    {
        $this->assertConfigurationIsInvalid(
            [
                'checkout_resolver' => [
                    'pattern' => 1,
                ],
            ]
        );

        $this->assertConfigurationIsInvalid(
            [
                'checkout_resolver' => [
                    'pattern' => true,
                ],
            ]
        );

        $this->assertConfigurationIsInvalid(
            [
                'checkout_resolver' => [
                    'pattern' => 1.24,
                ],
            ]
        );

        $this->assertConfigurationIsInvalid(
            [
                'checkout_resolver' => [
                    'pattern' => [],
                ],
            ]
        );
    }

    /**
     * @test
     */
    public function its_checkout_route_map_it_is_configurable()
    {
        $this->assertProcessedConfigurationEquals(
            [
                [
                    'checkout_resolver' => [
                        'route_map' => [
                            'addressed' => [
                                'route' => 'sylius_shop_checkout_select_shipping',
                            ],
                        ],
                    ]
                ]
            ],
            [
                'checkout_resolver' => [
                    'enabled' => true,
                    'pattern' => '/checkout/.+',
                    'route_map' => [
                        'addressed' => [
                            'route' => 'sylius_shop_checkout_select_shipping',
                        ],
                    ],
                ]
            ]
        );
    }

    /**
     * @test
     */
    public function its_checkout_route_map_route_cannot_be_empty()
    {
        $this->assertConfigurationIsInvalid(
            [
                'checkout_resolver' => [
                    'route_map' => [
                        'addressed' => [
                        ],
                    ],
                ]
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
