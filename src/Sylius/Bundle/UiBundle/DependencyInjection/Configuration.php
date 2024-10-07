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
                                    ->validate()
                                        ->ifTrue(static function (array $block): bool {
                                            if (!array_key_exists('template', $block) || !array_key_exists('component', $block)) {
                                                return false;
                                            }

                                            return null !== $block['template'] && [] !== $block['component'];
                                        })
                                        ->thenInvalid('You cannot use both "template" and "component" for a block.')
                                    ->end()
                                    ->children()
                                        ->booleanNode('enabled')->defaultNull()->end()
                                        ->arrayNode('context')->addDefaultsIfNotSet()->ignoreExtraKeys(false)->end()
                                        ->scalarNode('template')->defaultNull()->end()
                                        ->arrayNode('component')
                                            ->beforeNormalization()
                                                ->ifString()
                                                ->then(static fn (?string $component): array => ['name' => $component])
                                            ->end()
                                            ->treatNullLike([])
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('name')->isRequired()->end()
                                                ->arrayNode('inputs')->ignoreExtraKeys(false)->end()
                                            ->end()
                                        ->end()
                                        ->integerNode('priority')->defaultNull()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('twig_ux')
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
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
