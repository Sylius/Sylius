<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\GridBundle\Tests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Sylius\Bundle\GridBundle\DependencyInjection\Configuration;
use Sylius\Bundle\GridBundle\Doctrine\ORM\Driver;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function it_requires_only_grid_name()
    {
        $this->assertProcessedConfigurationEquals(
            [[
                'grids' => [
                    'sylius_admin_tax_category' => null,
                ],
            ]],
            [
                'grids' => [
                    'sylius_admin_tax_category' => [
                        'driver' => [
                            'name' => Driver::NAME,
                            'options' => [],
                        ],
                        'sorting' => [],
                        'fields' => [],
                        'filters' => [],
                        'actions' => [],
                    ],
                ],
            ],
            'grids'
        );
    }

    /**
     * @test
     */
    public function it_uses_doctrine_orm_as_default_driver()
    {
        $this->assertProcessedConfigurationEquals(
            [[]],
            ['drivers' => ['doctrine/orm']],
            'drivers'
        );
    }

    /**
     * @test
     */
    public function it_has_empty_action_and_filter_templates_by_default()
    {
        $this->assertProcessedConfigurationEquals(
            [[]],
            [
                'templates' => [
                    'action' => [],
                    'filter' => [],
                ],
            ],
            'templates'
        );
    }

    /**
     * @test
     */
    public function its_driver_cannot_be_empty()
    {
        $this->assertConfigurationIsInvalid([[
            'grids' => [
                'sylius_admin_tax_category' => [
                    'driver' => [
                        'name' => null,
                    ],
                ],
            ],
        ]]);
    }

    /**
     * @test
     */
    public function it_requires_field_type_to_be_defined()
    {
        $this->assertConfigurationIsInvalid([[
            'grids' => [
                'sylius_admin_tax_category' => [
                    'fields' => [
                        'code' => [
                            'label' => 'Internal code',
                        ],
                    ],
                ],
            ],
        ]]);
    }

    /**
     * @test
     */
    public function its_base_sorting_can_be_overwritten()
    {
        $this->assertProcessedConfigurationEquals(
            [
                ['grids' => [
                    'sylius_admin_tax_category' => [
                        'sorting' => ['code' => 'asc'],
                    ],
                ]],
                ['grids' => [
                    'sylius_admin_tax_category' => [
                        'sorting' => ['name' => 'desc'],
                    ],
                ]],
            ],
            ['grids' => [
                'sylius_admin_tax_category' => [
                    'sorting' => ['name' => 'desc'],
                ],
            ]],
            'grids.*.sorting'
        );
    }

    /**
     * @test
     */
    public function its_sorting_order_can_be_only_ascending_or_descending()
    {
        $this->assertConfigurationIsValid([[
            'grids' => [
                'sylius_admin_tax_category' => [
                    'sorting' => ['code' => 'asc'],
                ],
            ],
        ]]);

        $this->assertConfigurationIsValid([[
            'grids' => [
                'sylius_admin_tax_category' => [
                    'sorting' => ['code' => 'desc'],
                ],
            ],
        ]]);

        $this->assertConfigurationIsInvalid([[
            'grids' => [
                'sylius_admin_tax_category' => [
                    'sorting' => ['code' => 'left'],
                ],
            ],
        ]]);

        $this->assertConfigurationIsInvalid([[
            'grids' => [
                'sylius_admin_tax_category' => [
                    'sorting' => ['code' => null],
                ],
            ],
        ]]);
    }

    /**
     * @test
     */
    public function it_should_throw_an_exception_if_an_invalid_driver_is_enabled()
    {
        $this->assertConfigurationIsInvalid([[
            'drivers' => ['doctrine/orm', 'foo/invalid'],
        ]]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new Configuration();
    }
}
