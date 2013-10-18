<?php

namespace Sylius\Bundle\ReviewBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * {@inheritdoc}
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
