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

use Sylius\Bundle\SearchBundle\Controller\SearchController;
use Sylius\Bundle\SearchBundle\Model\SearchIndex;
use Sylius\Bundle\SearchBundle\Model\SearchIndexInterface;
use Sylius\Bundle\SearchBundle\Model\SearchLog;
use Sylius\Bundle\SearchBundle\Model\SearchLogInterface;
use Sylius\Component\Resource\Factory\Factory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
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
     * @param ArrayNodeDefinition $node
     */
    private function addFilterSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('filters')
                    ->performNoDeepMerging()
                    ->isRequired()
                    ->children()
                        ->arrayNode('pre_search_filter')
                            ->isRequired()
                            ->children()
                                ->scalarNode('taxon')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
                                ->booleanNode('enabled')
                                    ->isRequired()
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
                                        ->performNoDeepMerging()
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
                            ->performNoDeepMerging()
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
     * @param ArrayNodeDefinition $node
     */
    private function addAccessorsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->scalarNode('custom_accessor')->defaultValue(PropertyAccessor::class)->end()
            ->end()
        ;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addClassesSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('search')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('driver')->defaultValue('doctrine/orm')->cannotBeEmpty()->end()
                                        ->scalarNode('model')->defaultValue(SearchIndex::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(SearchIndexInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(SearchController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('log')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('driver')->defaultValue('doctrine/orm')->cannotBeEmpty()->end()
                                        ->scalarNode('model')->defaultValue(SearchLog::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(SearchLogInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(SearchController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
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
