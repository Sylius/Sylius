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

namespace Sylius\Bundle\ResourceBundle\Routing;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('routing');

        $rootNode
            ->children()
                ->scalarNode('alias')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('path')->defaultValue(null)->end()
                ->scalarNode('identifier')->defaultValue('id')->end()
                ->arrayNode('criteria')
                    ->useAttributeAsKey('identifier')
                    ->prototype('scalar')
                    ->end()
                ->end()
                ->booleanNode('filterable')->end()
                ->variableNode('form')->cannotBeEmpty()->end()
                ->scalarNode('serialization_version')->cannotBeEmpty()->end()
                ->scalarNode('section')->cannotBeEmpty()->end()
                ->scalarNode('redirect')->cannotBeEmpty()->end()
                ->scalarNode('templates')->cannotBeEmpty()->end()
                ->scalarNode('grid')->cannotBeEmpty()->end()
                ->booleanNode('permission')->defaultValue(false)->end()
                ->arrayNode('except')
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('only')
                    ->prototype('scalar')->end()
                ->end()
                ->variableNode('vars')->cannotBeEmpty()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
