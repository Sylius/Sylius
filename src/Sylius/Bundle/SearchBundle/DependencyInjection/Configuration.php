<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SearchBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 *
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sylius_search');

        $this->addFormSection($rootNode);

        $this->addFilterSection($rootNode);

        $this->addDriverSection($rootNode);

        $this->addIndexesSection($rootNode);

        return $treeBuilder;
    }

    /**
     * adds form section
     *
     * @param ArrayNodeDefinition $node
     */
    private function addFormSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->scalarNode('search_form_template')
                    ->info('Define the search form')
                ->end()
            ->end()
        ;
    }

    /**
     * adds filter section
     *
     * @param ArrayNodeDefinition $node
     */
    private function addFilterSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('filters')
                    ->children()
                        ->arrayNode('pre_search_filter')
                            ->children()
                                ->scalarNode('enabled')->end()
                                ->scalarNode('taxonomy')->end()
                            ->end()
                        ->end()
                        ->arrayNode('finders')
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('facet_group')->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('facet_groups')
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->children()
                                    ->arrayNode('values')
                                        ->useAttributeAsKey('name')
                                        ->prototype('scalar')
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('facets')
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('display_name')->end()
                                    ->scalarNode('type')->end()
                                    ->scalarNode('value')->end()
                                    ->arrayNode('values')
                                        ->prototype('array')
                                            ->children()
                                                ->scalarNode('from')->end()
                                                ->scalarNode('to')->end()
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

    /**
     * adds driver section
     *
     * @param ArrayNodeDefinition $node
     */
    private function addDriverSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->scalarNode('driver')
                    ->info('Defaults to the first client defined')
                ->end()
            ->end()
        ;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addIndexesSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('orm_indexes')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('class')
                            ->end()
                            ->arrayNode('mappings')
                                ->prototype('array')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

}
