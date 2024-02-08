<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ShopBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('sylius_shop');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->enumNode('locale_switcher')->values(['storage', 'url'])->defaultValue('url')->end()
                ->scalarNode('firewall_context_name')->defaultValue('shop')->end()
                ->arrayNode('checkout_resolver')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')
                            ->defaultTrue()
                        ->end()
                        ->scalarNode('pattern')
                            ->defaultValue('/checkout/.+')
                            ->validate()
                            ->ifTrue(
                                /** @param mixed $pattern */
                                fn ($pattern) => !is_string($pattern),
                            )
                                ->thenInvalid('Invalid pattern "%s"')
                            ->end()
                        ->end()
                        ->arrayNode('route_map')
                            ->useAttributeAsKey('name')
                            ->arrayPrototype()
                                ->children()
                                    ->scalarNode('route')
                                        ->cannotBeEmpty()
                                        ->isRequired()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('product_grid')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('include_all_descendants')
                            ->defaultFalse()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
