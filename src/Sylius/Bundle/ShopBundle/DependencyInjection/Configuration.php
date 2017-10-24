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

namespace Sylius\Bundle\ShopBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sylius_shop');

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
                            ->ifTrue(function ($pattern) {
                                return !is_string($pattern);
                            })
                                ->thenInvalid('Invalid pattern "%s"')
                            ->end()
                        ->end()
                        ->arrayNode('route_map')
                            ->useAttributeAsKey('name')
                            ->prototype('array')
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
            ->end()
        ;

        return $treeBuilder;
    }
}
