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
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function it_requires_only_grid_name_and_uses_doctrine_orm_as_default_driver()
    {
        $this->assertProcessedConfigurationEquals(
            [
                [
                    'grids' => [
                        'sylius_admin_tax_category' => null
                    ],
                ],
            ],
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
                    ]
                ],
                'drivers' => [ 'doctrine/orm' ]
            ]
        );
    }

    /**
     * @test
     */
    public function its_driver_cannot_be_empty()
    {
        $this->assertConfigurationIsInvalid(
            [
                'grids' => [
                    'sylius_admin_tax_category' => [
                        'driver' => [
                            'name' => null
                        ]
                    ]
                ]
            ]
        );
    }

    /**
     * @test
     */
    public function it_requires_field_type_to_be_defined()
    {
        $this->assertConfigurationIsInvalid(
            [
                'grids' => [
                    'sylius_admin_tax_category' => [
                        'fields' => [
                            'code' => [
                                'label' => 'Internal code'
                            ]
                        ]
                    ]
                ]
            ]
        );
    }

    /**
     * @test
     */
    public function it_requires_sorting_path_to_be_defined()
    {
        $this->assertConfigurationIsInvalid(
            [
                'grids' => [
                    'sylius_admin_tax_category' => [
                        'sorting' => [
                            'code' => [
                                'direction' => 'desc',
                            ]
                        ]
                    ]
                ]
            ]
        );
    }

    /**
     * @test
     */
    public function its_sorting_direction_can_be_only_ascending_or_descending()
    {
        $this->assertConfigurationIsValid(
            [[
                'grids' => [
                    'sylius_admin_tax_category' => [
                        'sorting' => [
                            'code' => [
                                'path' => 'code',
                                'direction' => 'asc',
                            ]
                        ]
                    ]
                ]
            ]],
            'grids.*.sorting.*'
        );

        $this->assertConfigurationIsValid(
            [[
                'grids' => [
                    'sylius_admin_tax_category' => [
                        'sorting' => [
                            'code' => [
                                'path' => 'code',
                                'direction' => 'desc',
                            ]
                        ]
                    ]
                ]
            ]]
        );

        $this->assertConfigurationIsInvalid(
            [
                'grids' => [
                    'sylius_admin_tax_category' => [
                        'sorting' => [
                            'code' => [
                                'path' => 'code',
                                'direction' => 'left',
                            ]
                        ]
                    ]
                ]
            ]
        );
    }

    /**
     * @test
     */
    public function it_does_not_require_sorting_direction_and_uses_descending_by_default()
    {
        $this->assertProcessedConfigurationEquals(
            [[
                'grids' => [
                    'sylius_admin_tax_category' => [
                        'sorting' => [
                            'code' => [
                                'path' => 'code'
                            ]
                        ]
                    ]
                ]
            ]],
            [
                'grids' => [
                    'sylius_admin_tax_category' => [
                        'driver' => [
                            'name' => Driver::NAME,
                            'options' => [],
                        ],
                        'sorting' => [
                            'code' => [
                                'path' => 'code',
                                'direction' => 'desc',
                            ]
                        ],
                        'fields' => [],
                        'filters' => [],
                        'actions' => [],
                    ]
                ],
                'drivers' => [ 'doctrine/orm' ]
            ]
        );
    }

    /**
     * @test
     */
    public function it_should_throw_an_exception_if_an_invalid_driver_is_enabled()
    {
        $this->assertConfigurationIsInvalid(
            [
                [
                    'drivers' => [ 'doctrine/orm', 'foo/invalid' ],
                ],
            ],
            'Invalid driver specified in ["doctrine\/orm","foo\/invalid"], valid drivers:'
        );
    }

    protected function getConfiguration()
    {
        return new Configuration();
    }
}
