<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Tests\DependencyInjection;

use ReflectionMethod;
use Sylius\Bundle\ResourceBundle\DependencyInjection\AbstractResourceConfiguration;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;

/**
 * @author Aleksey Bannov <a.s.bannov@gmail.com>
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Processor
     */
    protected $processor;

    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->processor = new Processor();

        $this->configuration = new Configuration();
    }

    public function testConfiguration()
    {
        $configuration = $this->processor->processConfiguration($this->configuration, array(
            'sylius_resource' => array(
                'resources' => array(
                    'product' => array(
                        'driver'         => 'doctrine/orm',
                        'object_manager' => 'default',
                        'classes'        => array(
                            'model' => 'Sylius\Product',
                            'form'  => 'Sylius\ProductType',
                        )
                    ),
                ),
            )
        ))
        ;

        $this->assertEquals(array(
            'resources' => array(
                'product' => array(
                    'driver'           => 'doctrine/orm',
                    'object_manager'   => 'default',
                    'classes'          => array(
                        'model'      => 'Sylius\Product',
                        'controller' => '%sylius.default.controller.class%',
                        'form'       => array(
                            AbstractResourceConfiguration::DEFAULT_KEY => 'Sylius\ProductType',
                        ),
                        'repository' => '',
                        'interface'  => '',
                    ),
                    'validation_group' => array(),
                ),
            ),
            'settings'  => array(
                'paginate'          => '',
                'limit'             => '',
                'allowed_paginate'  => array(10, 20, 30),
                'default_page_size' => 10,
                'sortable'          => '',
                'sorting'           => '',
                'filterable'        => '',
                'criteria'          => '',
            ),
        ), $configuration)
        ;
    }
}
