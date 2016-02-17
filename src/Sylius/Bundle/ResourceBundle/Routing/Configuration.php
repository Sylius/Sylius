<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Routing;

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
        $rootNode = $treeBuilder->root('routing');

        $rootNode
            ->children()
                ->scalarNode('alias')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('path')->cannotBeEmpty()->end()
                ->scalarNode('form')->cannotBeEmpty()->end()
                ->scalarNode('section')->cannotBeEmpty()->end()
                ->scalarNode('redirect')->cannotBeEmpty()->end()
                ->scalarNode('templates')->cannotBeEmpty()->end()
                ->arrayNode('except')
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('only')
                    ->prototype('scalar')->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
