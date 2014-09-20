<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle.
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Configuration extends AbstractResourceConfiguration
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sylius_resource');

        $this->addResourcesSection($rootNode);
        $this->addSettingsSection($rootNode);

        return $treeBuilder;
    }

    /**
     * Adds `resources` section.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addResourcesSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('resources')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->append($this->createDriverNode())
                            ->append($this->createObjectManagerNode('default'))
                            ->append($this->createTemplateNode())
                            ->append($this->createValidationGroupNode())
                            ->append($this->createClassesNode())
                            ->scalarNode('translatable_fields')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function createClassesNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('classes');
        $this->addClassesSection($node, array());

        return $node;
    }

    /**
     * Adds `settings` section.
     *
     * @param $node
     */
    private function addSettingsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('settings')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->variableNode('paginate')->defaultNull()->end()
                        ->variableNode('limit')->defaultNull()->end()
                        ->arrayNode('allowed_paginate')
                            ->prototype('integer')->end()
                            ->defaultValue(array(10, 20, 30))
                        ->end()
                        ->integerNode('default_page_size')->defaultValue(10)->end()
                        ->booleanNode('sortable')->defaultFalse()->end()
                        ->variableNode('sorting')->defaultNull()->end()
                        ->booleanNode('filterable')->defaultFalse()->end()
                        ->variableNode('criteria')->defaultNull()->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
