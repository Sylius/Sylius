<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('sylius_sales');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('driver')->cannotBeOverwritten()->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('engine')->defaultValue('twig')->end()
                ->end()
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
                        ->arrayNode('model')
                            ->addDefaultsIfNotSet()
                            ->isRequired()
                            ->children()
                                ->scalarNode('order')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('item')->defaultValue('')->end()
                                ->scalarNode('status')->isRequired()->cannotBeEmpty()->end()
                            ->end()
                        ->end()
                        ->arrayNode('controller')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('frontend')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('order')->defaultValue('Sylius\\Bundle\\SalesBundle\\Controller\\Frontend\\OrderController')->end()
                                    ->end()
                                ->end()
                                ->arrayNode('backend')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('order')->defaultValue('Sylius\\Bundle\\SalesBundle\\Controller\\Backend\\OrderController')->end()
                                        ->scalarNode('status')->defaultValue('Sylius\\Bundle\\SalesBundle\\Controller\\Backend\\StatusController')->end()
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
                                        ->scalarNode('order')->defaultValue('Sylius\\Bundle\\SalesBundle\\Form\\Type\\OrderType')->end()
                                        ->scalarNode('item')->defaultValue('Sylius\\Bundle\\SalesBundle\\Form\\Type\\ItemType')->end()
                                        ->scalarNode('status')->defaultValue('Sylius\\Bundle\\SalesBundle\\Form\\Type\\StatusType')->end()
                                        ->scalarNode('status_choice')->defaultValue('Sylius\\Bundle\\SalesBundle\\Form\\Type\\StatusChoiceType')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('manipulator')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('order')->defaultValue('Sylius\\Bundle\\SalesBundle\\Manipulator\\OrderManipulator')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * Adds `extensions` section.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addExtensionsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('extensions')
                    ->children()
                        ->arrayNode('confirmation')
                            ->children()
                                ->booleanNode('enabled')->end()
                                ->arrayNode('options')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->arrayNode('email')
                                            ->children()
                                                ->scalarNode('from')->defaultValue('no-reply@example.com')->end()
                                                ->scalarNode('subject')->defaultValue('Confirm your order on example.com.')->end()
                                                ->scalarNode('template')->defaultValue('SyliusSalesBundle:Frontend/Confirmation:email.html.twig')->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
