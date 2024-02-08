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
            ->fixXmlConfig('event')
            ->children()
                ->booleanNode('use_webpack')->defaultTrue()->end()
                ->arrayNode('events')
                    ->useAttributeAsKey('event_name')
                    ->arrayPrototype()
                        ->fixXmlConfig('block')
                        ->children()
                            ->arrayNode('blocks')
                                ->defaultValue([])
                                ->useAttributeAsKey('block_name')
                                ->arrayPrototype()
                                    ->canBeDisabled()
                                    ->beforeNormalization()
                                        ->ifString()
                                        ->then(static fn (?string $template): array => ['template' => $template])
                                    ->end()
                                    ->children()
                                        ->booleanNode('enabled')->defaultNull()->end()
                                        ->arrayNode('context')->addDefaultsIfNotSet()->ignoreExtraKeys(false)->end()
                                        ->scalarNode('template')->defaultNull()->end()
                                        ->integerNode('priority')->defaultNull()->end()
        ;

        return $treeBuilder;
    }
}
