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

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\GridBundle\DependencyInjection\SyliusGridExtension;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class SyliusGridExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @test
     */
    public function it_sets_configured_grids_as_parameter()
    {
        $this->load([
            'grids' => [
                'sylius_admin_tax_category' => [
                    'driver' => [
                        'name' => 'doctrine/orm',
                        'options' => [
                            'class' => 'Sylius\Component\Taxation\Model\TaxCategory'
                        ]
                    ]
                ]
            ]
        ]);

        $this->assertContainerBuilderHasParameter('sylius.grids_definitions', [
            'sylius_admin_tax_category' => [
                'driver' => [
                    'name' => 'doctrine/orm',
                    'options' => [
                        'class' => 'Sylius\Component\Taxation\Model\TaxCategory'
                    ]
                ],
                'sorting' => [],
                'fields' => [],
                'filters' => [],
                'actions' => [],
            ]
        ]);
    }

    /**
     * @test
     */
    public function it_aliases_default_services()
    {
        $this->load([]);

        $this->assertContainerBuilderHasAlias('sylius.grid.renderer', 'sylius.grid.renderer.twig');
        $this->assertContainerBuilderHasAlias('sylius.grid.data_extractor', 'sylius.grid.data_extractor.property_access');
    }

    /**
     * @test
     */
    public function it_always_defines_template_parameters()
    {
        $this->load([]);

        $this->assertContainerBuilderHasParameter('sylius.grid.templates.filter', []);
        $this->assertContainerBuilderHasParameter('sylius.grid.templates.action', []);
    }

    /**
     * @test
     */
    public function it_sets_filter_templates_as_parameters()
    {
        $this->load([
            'templates' => [
                'filter' => [
                    'string' => 'AppBundle:Grid/Filter:string.html.twig',
                    'date' => 'AppBundle:Grid/Filter:date.html.twig',
                ]
            ]
        ]);

        $this->assertContainerBuilderHasParameter('sylius.grid.templates.filter', [
            'string' => 'AppBundle:Grid/Filter:string.html.twig',
            'date' => 'AppBundle:Grid/Filter:date.html.twig',
        ]);
    }

    /**
     * @test
     */
    public function it_sets_action_templates_as_parameters()
    {
        $this->load([
            'templates' => [
                'action' => [
                    'create' => 'AppBundle:Grid/Filter:create.html.twig',
                    'update' => 'AppBundle:Grid/Filter:update.html.twig',
                ]
            ]
        ]);

        $this->assertContainerBuilderHasParameter('sylius.grid.templates.action', [
            'create' => 'AppBundle:Grid/Filter:create.html.twig',
            'update' => 'AppBundle:Grid/Filter:update.html.twig',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensions()
    {
        return [
            new SyliusGridExtension(),
        ];
    }
}
