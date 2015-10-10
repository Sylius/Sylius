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
                            ->defaultValue(array('sylius', 'sylius_review'))
                        ->end()
                        ->arrayNode('review_admin')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius'))
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addClassesSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('classes')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('subject')->isRequired()->end()
                            ->arrayNode('review')
                                ->isRequired()
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('model')->isRequired()->end()
                                    ->scalarNode('controller')->defaultValue('Sylius\Bundle\ResourceBundle\Controller\ResourceController')->end()
                                    ->arrayNode('form')
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->scalarNode('default')->defaultValue('Sylius\Bundle\ReviewBundle\Form\Type\ReviewType')->end()
                                            ->scalarNode('admin')->isRequired()->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('reviewer')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('model')->isRequired()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
