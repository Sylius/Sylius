<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InventoryBundle\DependencyInjection;

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
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sylius_inventory');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('driver')->isRequired()->cannotBeEmpty()->end()
                ->booleanNode('backorders')->defaultTrue()->end()
                ->booleanNode('track_inventory')->defaultTrue()->end()
                ->scalarNode('checker')->defaultValue('sylius.availability_checker.default')->cannotBeEmpty()->end()
                ->scalarNode('operator')->cannotBeEmpty()->end()
                ->arrayNode('events')->prototype('scalar')->end()
            ->end()
        ->end()
        ->validate()
            ->ifTrue(function($array){
                return !isset($array['operator']);
            })
            ->then(function($array){
                $array['operator'] = 'sylius.inventory_operator.'.($array['track_inventory'] ? 'default' : 'noop');
                return $array;
            })
        ->end();

        $this->addClassesSection($rootNode);

        return $treeBuilder;
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
                    ->isRequired()
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('inventory_unit')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Sylius\Component\Inventory\Model\InventoryUnit')->end()
                                ->scalarNode('controller')->defaultValue('Sylius\Bundle\InventoryBundle\Controller\InventoryUnitController')->end()
                                ->scalarNode('repository')->end()
                            ->end()
                        ->end()
                        ->arrayNode('stockable')
                            ->isRequired()
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('controller')->defaultValue('Sylius\Bundle\ResourceBundle\Controller\ResourceController')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
