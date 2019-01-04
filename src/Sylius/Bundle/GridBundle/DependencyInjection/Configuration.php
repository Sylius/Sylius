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

namespace Sylius\Bundle\GridBundle\DependencyInjection;

use Sylius\Bundle\GridBundle\Doctrine\ORM\Driver as DoctrineORMDriver;
use Sylius\Bundle\GridBundle\SyliusGridBundle;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        if (method_exists(TreeBuilder::class, 'getRootNode')) {
            $treeBuilder = new TreeBuilder('sylius_grid');
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $treeBuilder = new TreeBuilder();
            $rootNode = $treeBuilder->root('sylius_grid');
        }

        $this->addDriversSection($rootNode);
        $this->addTemplatesSection($rootNode);
        $this->addGridsSection($rootNode);

        return $treeBuilder;
    }

    private function addDriversSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('drivers')
                    ->defaultValue([SyliusGridBundle::DRIVER_DOCTRINE_ORM])
                    ->enumPrototype()->values(SyliusGridBundle::getAvailableDrivers())->end()
                ->end()
            ->end()
        ;
    }

    private function addTemplatesSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('templates')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('filter')
                            ->useAttributeAsKey('name')
                            ->scalarPrototype()->end()
                        ->end()
                        ->arrayNode('action')
                            ->useAttributeAsKey('name')
                            ->scalarPrototype()->end()
                        ->end()
                        ->arrayNode('bulk_action')
                            ->useAttributeAsKey('name')
                            ->scalarPrototype()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addGridsSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('grids')
                    ->useAttributeAsKey('code')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('extends')->cannotBeEmpty()->end()
                            ->arrayNode('driver')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('name')->cannotBeEmpty()->defaultValue(DoctrineORMDriver::NAME)->end()
                                    ->arrayNode('options')
                                        ->performNoDeepMerging()
                                        ->variablePrototype()->end()
                                        ->defaultValue([])
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('sorting')
                                ->performNoDeepMerging()
                                ->useAttributeAsKey('name')
                                ->enumPrototype()->values(['asc', 'desc'])->cannotBeEmpty()->end()
                            ->end()
                            ->arrayNode('limits')
                                ->performNoDeepMerging()
                                ->integerPrototype()->end()
                                ->defaultValue([10, 25, 50])
                            ->end()
                            ->arrayNode('fields')
                                ->useAttributeAsKey('name')
                                ->arrayPrototype()
                                    ->children()
                                        ->scalarNode('type')->isRequired()->cannotBeEmpty()->end()
                                        ->scalarNode('label')->cannotBeEmpty()->end()
                                        ->scalarNode('path')->cannotBeEmpty()->end()
                                        ->scalarNode('sortable')->end()
                                        ->scalarNode('enabled')->defaultTrue()->end()
                                        ->scalarNode('position')->defaultValue(100)->end()
                                        ->arrayNode('options')
                                            ->performNoDeepMerging()
                                            ->variablePrototype()->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('filters')
                                ->useAttributeAsKey('name')
                                ->arrayPrototype()
                                    ->children()
                                        ->scalarNode('type')->isRequired()->cannotBeEmpty()->end()
                                        ->scalarNode('label')->cannotBeEmpty()->end()
                                        ->scalarNode('enabled')->defaultTrue()->end()
                                        ->scalarNode('template')->end()
                                        ->scalarNode('position')->defaultValue(100)->end()
                                        ->arrayNode('options')
                                            ->performNoDeepMerging()
                                            ->variablePrototype()->end()
                                        ->end()
                                        ->arrayNode('form_options')
                                            ->performNoDeepMerging()
                                            ->variablePrototype()->end()
                                        ->end()
                                        ->variableNode('default_value')->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('actions')
                                ->useAttributeAsKey('name')
                                ->arrayPrototype()
                                    ->useAttributeAsKey('name')
                                    ->arrayPrototype()
                                        ->children()
                                            ->scalarNode('type')->isRequired()->end()
                                            ->scalarNode('label')->end()
                                            ->scalarNode('enabled')->defaultTrue()->end()
                                            ->scalarNode('icon')->end()
                                            ->scalarNode('position')->defaultValue(100)->end()
                                            ->arrayNode('options')
                                                ->performNoDeepMerging()
                                                ->variablePrototype()->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
