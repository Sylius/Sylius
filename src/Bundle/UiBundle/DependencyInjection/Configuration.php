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

namespace Sylius\Bundle\UiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('sylius_ui');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('twig_ux')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('live_component_tags')
                            ->useAttributeAsKey('name')
                            ->variablePrototype()
                                ->validate()
                                    ->ifTrue(function ($tagOptions) {
                                        return !is_array($tagOptions) || !array_key_exists('route', $tagOptions);
                                    })
                                    ->thenInvalid('The "route" attribute is required for the child of "sylius_ui.twig_ux.live_component_tags".')
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('anonymous_component_template_prefixes')
                            ->useAttributeAsKey('prefix_name')
                            ->scalarPrototype()->end()
                        ->end()
                        ->scalarNode('component_default_template')->cannotBeEmpty()->defaultValue('@SyliusUi/components/default.html.twig')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
