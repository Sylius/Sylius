<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle.
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sylius_order');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('driver')->isRequired()->cannotBeEmpty()->end()
            ->end()
        ;

        $this->addClassesSection($rootNode);
        $this->addValidationGroupsSection($rootNode);

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
                        ->arrayNode('order')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius'))
                        ->end()
                        ->arrayNode('order_item')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius'))
                        ->end()
                        ->arrayNode('adjustment')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius'))
                        ->end()
                    ->end()
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
                        ->arrayNode('order')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Sylius\\Bundle\\OrderBundle\\Model\\Order')->end()
                                ->scalarNode('controller')->defaultValue('Sylius\\Bundle\\OrderBundle\\Controller\\OrderController')->end()
                                ->scalarNode('repository')->cannotBeEmpty()->end()
                                ->scalarNode('form')->defaultValue('Sylius\\Bundle\\OrderBundle\\Form\\Type\\OrderType')->end()
                            ->end()
                        ->end()
                        ->arrayNode('order_item')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Sylius\\Bundle\\OrderBundle\\Model\\OrderItem')->end()
                                ->scalarNode('controller')->defaultValue('Sylius\\Bundle\\ResourceBundle\\Controller\\ResourceController')->end()
                                ->scalarNode('repository')->cannotBeEmpty()->end()
                                ->scalarNode('form')->defaultValue('Sylius\\Bundle\\OrderBundle\\Form\\Type\\OrderItemType')->end()
                            ->end()
                        ->end()
                        ->arrayNode('adjustment')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Sylius\\Bundle\\OrderBundle\\Model\\Adjustment')->end()
                                ->scalarNode('controller')->defaultValue('Sylius\\Bundle\\ResourceBundle\\Controller\\ResourceController')->end()
                                ->scalarNode('repository')->cannotBeEmpty()->end()
                                ->scalarNode('form')->defaultValue('Sylius\\Bundle\\OrderBundle\\Form\\Type\\AdjustmentType')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
