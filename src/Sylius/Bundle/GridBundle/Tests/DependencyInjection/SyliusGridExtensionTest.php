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

namespace Sylius\Bundle\GridBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\GridBundle\DependencyInjection\SyliusGridExtension;

final class SyliusGridExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @test
     */
    public function it_sets_configured_grids_as_parameter(): void
    {
        $this->load([
            'grids' => [
                'sylius_admin_tax_category' => [
                    'driver' => [
                        'name' => 'doctrine/orm',
                        'options' => [
                            'class' => 'Sylius\Component\Taxation\Model\TaxCategory',
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertContainerBuilderHasParameter('sylius.grids_definitions', [
            'sylius_admin_tax_category' => [
                'driver' => [
                    'name' => 'doctrine/orm',
                    'options' => [
                        'class' => 'Sylius\Component\Taxation\Model\TaxCategory',
                    ],
                ],
                'sorting' => [],
                'limits' => [10, 25, 50],
                'fields' => [],
                'filters' => [],
                'actions' => [],
            ],
        ]);
    }

    /**
     * @test
     */
    public function it_aliases_default_services(): void
    {
        $this->load([]);

        $this->assertContainerBuilderHasAlias('sylius.grid.renderer', 'sylius.grid.renderer.twig');
        $this->assertContainerBuilderHasAlias('sylius.grid.data_extractor', 'sylius.grid.data_extractor.property_access');
    }

    /**
     * @test
     */
    public function it_always_defines_template_parameters(): void
    {
        $this->load([]);

        $this->assertContainerBuilderHasParameter('sylius.grid.templates.filter', []);
        $this->assertContainerBuilderHasParameter('sylius.grid.templates.action', []);
    }

    /**
     * @test
     */
    public function it_sets_filter_templates_as_parameters(): void
    {
        $this->load([
            'templates' => [
                'filter' => [
                    'string' => 'Grid/Filter/string.html.twig',
                    'date' => 'Grid/Filter/date.html.twig',
                ],
            ],
        ]);

        $this->assertContainerBuilderHasParameter('sylius.grid.templates.filter', [
            'string' => 'Grid/Filter/string.html.twig',
            'date' => 'Grid/Filter/date.html.twig',
        ]);
    }

    /**
     * @test
     */
    public function it_sets_action_templates_as_parameters(): void
    {
        $this->load([
            'templates' => [
                'action' => [
                    'create' => 'Grid/Filter/create.html.twig',
                    'update' => 'Grid/Filter/update.html.twig',
                ],
            ],
        ]);

        $this->assertContainerBuilderHasParameter('sylius.grid.templates.action', [
            'create' => 'Grid/Filter/create.html.twig',
            'update' => 'Grid/Filter/update.html.twig',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensions(): array
    {
        return [
            new SyliusGridExtension(),
        ];
    }
}
