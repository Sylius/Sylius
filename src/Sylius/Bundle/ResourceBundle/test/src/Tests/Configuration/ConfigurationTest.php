<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Tests;

use AppBundle\Entity\Book;
use AppBundle\Form\Type\BookType;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionConfigurationTestCase;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Configuration;
use Sylius\Bundle\ResourceBundle\DependencyInjection\SyliusResourceExtension;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType;
use Sylius\Component\Resource\Factory\Factory;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class ConfigurationTest extends AbstractExtensionConfigurationTestCase
{
    /**
     * @test
     */
    public function it_processes_resource_configuration()
    {
        $expectedConfiguration = [
            'resources' => [
                'app.book' => [
                    'classes' => [
                        'model' => Book::class,
                        'form' => [
                            'default' => BookType::class,
                            'choice' => ResourceChoiceType::class
                        ],
                        'controller' => ResourceController::class,
                        'factory' => Factory::class,
                    ],
                    'driver' => 'doctrine/orm',
                    'validation_groups' => [
                        'default' => []
                    ],
                ]
            ],
            'settings' => [
                'paginate' => null,
                'limit' => null,
                'allowed_paginate' => [
                    0 => 10,
                    1 => 20,
                    2 => 30,
                ],
                'default_page_size' => 10,
                'sortable' => false,
                'sorting' => null,
                'filterable' => false,
                'criteria' => null,
            ]
        ];

        $sources = [__DIR__.'/../../../app/config/resources.yml'];
        $this->assertProcessedConfigurationEquals($expectedConfiguration, $sources);
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerExtension()
    {
        return new SyliusResourceExtension();
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new Configuration();
    }
}
