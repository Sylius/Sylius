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

        $this->addQueryLoggerSection($rootNode);
        $this->addFilterSection($rootNode);
        $this->addDriverSection($rootNode);
        $this->addFormSection($rootNode);
        $this->addIndexesSection($rootNode);
        $this->addAccessorsSection($rootNode);
        $this->addClassesSection($rootNode);

        return $treeBuilder;
    }

    /**
     * Adds form section
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
     * Adds filter section
     *
     * @param ArrayNodeDefinition $node
     */
    private function addFilterSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('filters')
                    ->isRequired()
                    ->children()
                        ->arrayNode('pre_search_filter')
                            ->isRequired()
                            ->children()
                                ->scalarNode('taxonomy')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
                                ->booleanNode('enabled')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
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
     * Adds driver section
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
                ->scalarNode('engine')
                    ->info('Defaults to the first client defined')
                ->end()
                ->variableNode('request_method')->defaultValue('GET')->end()
            ->end()
        ;
    }

    /**
     * Adds query logger section
     *
     * @param ArrayNodeDefinition $node
     */
    private function addQueryLoggerSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('query_logger')
                    ->addDefaultsIfNotSet()
                        ->canBeEnabled()
                        ->children()
                            ->variableNode('driver')->defaultValue('orm')->end()
                            ->variableNode('engine')->defaultValue('orm')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * Adds indexes section
     *
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

    /**
     * Adds custom accessors section
     *
     * @param ArrayNodeDefinition $node
     */
    private function addAccessorsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('custom_accessors')
                ->prototype('scalar')
                ->end()
            ->end()
        ;
    }

    /**
     * Adds `classes` section.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addClassesSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('classes')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('search')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('driver')->defaultValue('doctrine/orm')->end()
                                ->scalarNode('model')->defaultValue('Sylius\Bundle\SearchBundle\Model\SearchIndex')->end()
                                ->scalarNode('controller')->defaultValue('Sylius\Bundle\SearchBundle\Controller\SearchController')->end()
                                ->scalarNode('repository')->end()
                            ->end()
                        ->end()
                        ->arrayNode('log')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('driver')->defaultValue('doctrine/orm')->end()
                                ->scalarNode('model')->defaultValue('Sylius\Bundle\SearchBundle\Model\SearchLog')->end()
                                ->scalarNode('controller')->defaultValue('Sylius\Bundle\SearchBundle\Controller\SearchController')->end()
                                ->scalarNode('repository')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
