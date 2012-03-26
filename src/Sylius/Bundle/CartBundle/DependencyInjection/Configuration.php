<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\DependencyInjection;

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
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sylius_cart');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('driver')->cannotBeOverwritten()->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('engine')->defaultValue('twig')->end()
                ->scalarNode('operator')->cannotBeOverwritten()->cannotBeEmpty()->end()
                ->scalarNode('resolver')->cannotBeOverwritten()->cannotBeEmpty()->end()
                ->scalarNode('storage')->defaultValue('sylius_cart.storage.session')->end()
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
                        ->scalarNode('provider')->defaultValue('Sylius\\Bundle\\CartBundle\\Provider\\CartProvider')->end()
                        ->arrayNode('model')
                            ->isRequired()
                            ->children()
                                ->scalarNode('cart')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('item')->isRequired()->cannotBeEmpty()->end()
                            ->end()
                        ->end()
                        ->arrayNode('controller')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('frontend')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('cart')->defaultValue('Sylius\Bundle\\CartBundle\\Controller\\Frontend\\CartController')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('form')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('type')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('cart')->defaultValue('Sylius\Bundle\CartBundle\Form\Type\CartFormType')->end()
                                        ->scalarNode('item')->defaultValue('Sylius\Bundle\CartBundle\Form\Type\ItemType')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('listener')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('request')->defaultValue('Sylius\Bundle\\CartBundle\\EventDispatcher\\Listener\\RequestListener')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
