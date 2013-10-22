<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReviewBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle.
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sylius_review');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('driver')->isRequired()->cannotBeEmpty()->end()
            ->end()
        ;

        $this->addClassesSection($rootNode);
        $this->addValidationGroupsSection($rootNode);

        return $treeBuilder;
    }

    /**
     * Adds `validation_groups` section.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addValidationGroupsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('validation_groups')
                ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('review')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius'))
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
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
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('review')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Sylius\\Bundle\\ReviewBundle\\Model\\Review')->end()
                                ->scalarNode('controller')->defaultValue('Sylius\\Bundle\\ReviewBundle\\Controller\\ReviewController')->end()
                                ->scalarNode('repository')->end()
                                ->scalarNode('form')->defaultValue('Sylius\\Bundle\\ReviewBundle\\Form\\Type\\ReviewType')->end()
                            ->end()
                        ->end()
                        ->arrayNode('guest_reviewer')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Sylius\\Bundle\\ReviewBundle\\Model\\GuestReviewer')->end()
                                ->scalarNode('controller')->end()
                                ->scalarNode('repository')->end()
                                ->scalarNode('form')->defaultValue('Sylius\\Bundle\\ReviewBundle\\Form\\Type\\GuestReviewerType')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
