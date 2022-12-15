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

namespace Sylius\Bundle\ApiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/** @experimental */
final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('sylius_api');

        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->booleanNode('enabled')
                    ->defaultFalse()
                ->end()
                ->booleanNode('legacy_error_handling')
                    ->defaultFalse()
                ->end()
            ->end()
            ->children()
                ->variableNode('product_image_prefix')
                    ->defaultValue('media/image')
                ->end()
            ->end()
            ->children()
                ->arrayNode('filter_eager_loading_extension')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('restricted_resources')
                            ->useAttributeAsKey('name')
                            ->arrayPrototype()
                                ->children()
                                    ->arrayNode('operations')
                                        ->useAttributeAsKey('name')
                                        ->arrayPrototype()
                                            ->canBeDisabled()
                                            ->children()
                                                ->booleanNode('enabled')->defaultTrue()->end()
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

        return $treeBuilder;
    }
}
