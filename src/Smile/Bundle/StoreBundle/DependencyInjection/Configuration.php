<?php

namespace Smile\Bundle\StoreBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('smile_store');

        $rootNode
            ->children()
            ->scalarNode('driver')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('storage')->defaultValue('sylius.storage.session')->end()
            ->end();

        $this->addValidationGroupsSection($rootNode);
        $this->addClassesSection($rootNode);

        return $treeBuilder;
    }

    /**
     * Adds `validation_groups` section.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addValidationGroupsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
            ->arrayNode('validation_groups')
            ->addDefaultsIfNotSet()
            ->children()
            ->arrayNode('store')
            ->prototype('scalar')->end()
            ->defaultValue(array('store'))
            ->end()
            ->end()
            ->end()
            ->end();
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
            ->arrayNode('store')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('model')->defaultValue('Sylius\Component\Store\Model\Store')->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end();
    }
}