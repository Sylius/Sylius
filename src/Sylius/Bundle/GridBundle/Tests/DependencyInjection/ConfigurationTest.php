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
    public function it_requires_only_grid_name_and_uses_doctrine_orm_as_default_driver()
    {
        $this->assertProcessedConfigurationEquals(
            [[
                'grids' => [
                    'sylius_admin_tax_category' => null
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
            [[
                'grids' => [
                    'sylius_admin_tax_category' => [
                        'driver' => [
                            'name' => null
                        ]
                    ]
                ]
            ]]
        );
    }

    /**
     * @test
     */
    public function it_requires_field_type_to_be_defined()
    {
        $this->assertConfigurationIsInvalid(
            [[
                'grids' => [
                    'sylius_admin_tax_category' => [
                        'fields' => [
                            'code' => [
                                'label' => 'Internal code'
                            ]
                        ]
                    ]
                ]
            ]]
        );
    }

    /**
     * @test
     */
    public function it_requires_sorting_default_field_to_be_defined()
    {
        $this->assertConfigurationIsInvalid(
            [[
                'grids' => [
                    'sylius_admin_tax_category' => [
                        'sorting'
                    ]
                ]
            ]]
        );
    }

    /**
     * @test
     */
    public function its_sorting_order_can_be_only_ascending_or_descending()
    {
        $this->assertConfigurationIsValid(
            [[
                'grids' => [
                    'sylius_admin_tax_category' => [
                        'sorting' => [
                            'code' => 'asc',
                        ]
                    ]
                ]
            ]]
        );

        $this->assertConfigurationIsValid(
            [[
                'grids' => [
                    'sylius_admin_tax_category' => [
                        'sorting' => [
                            'code' => 'desc',
                        ]
                    ]
                ]
            ]]
        );

        $this->assertConfigurationIsInvalid(
            [[
                'grids' => [
                    'sylius_admin_tax_category' => [
                        'sorting' => [
                            'code' => 'left',
                        ]
                    ]
                ]
            ]]
        );
    }

    /**
     * @test
     */
    public function it_does_require_sorting_order_to_be_declared()
    {
        $this->assertConfigurationIsInvalid(
            [[
                'grids' => [
                    'sylius_admin_tax_category' => [
                        'sorting' => [
                            'code',
                        ]
                    ]
                ]
            ]]
        );

        $this->assertConfigurationIsValid(
            [[
                'grids' => [
                    'sylius_admin_tax_category' => [
                        'sorting' => [
                            'code' => 'asc',
                        ]
                    ]
                ]
            ]]
        );
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

    protected function getConfiguration()
    {
        return new Configuration();
    }
}
