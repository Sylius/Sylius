<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\GridBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sylius_grid');

        $this->addGridsSection($rootNode);

        return $treeBuilder;
    }

    /**
     * Adds `grids` section.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addGridsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('grids')
                    ->useAttributeAsKey('code')
                    ->prototype('array')
                        ->children()
                            ->arrayNode('driver')
                                ->children()
                                    ->scalarNode('name')->defaultValue('doctrine/orm')->end()
                                    ->arrayNode('options')
                                        ->prototype('variable')->end()
                                        ->defaultValue(array())
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('sorting')
                                ->prototype('scalar')->end()
                            ->end()
                            ->arrayNode('fields')
                                ->useAttributeAsKey('name')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('type')->isRequired()->cannotBeEmpty()->end()
                                        ->scalarNode('path')->cannotBeEmpty()->end()
                                        ->scalarNode('label')->cannotBeEmpty()->end()
                                        ->arrayNode('options')
                                            ->prototype('variable')->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('filters')
                                ->useAttributeAsKey('name')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('type')->isRequired()->cannotBeEmpty()->end()
                                        ->scalarNode('label')->cannotBeEmpty()->end()
                                        ->arrayNode('options')
                                            ->prototype('variable')->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('actions')
                                ->useAttributeAsKey('name')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('type')->isRequired()->end()
                                        ->scalarNode('label')->end()
                                        ->arrayNode('options')
                                            ->prototype('variable')->end()
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
