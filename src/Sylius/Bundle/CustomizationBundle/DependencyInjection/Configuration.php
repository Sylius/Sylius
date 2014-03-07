<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CustomizationBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('sylius_customization');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('driver')->defaultNull()->end()
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
                        ->arrayNode('customization')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius'))
                        ->end()
                        ->arrayNode('customization_value')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius'))
                        ->end()
                        ->arrayNode('customization_subject')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius'))
                        ->end()
                        ->arrayNode('customization_subject_instance')
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
                        ->arrayNode('customization')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Sylius\\Component\\Customization\\Model\\Customization')->end()
                                ->scalarNode('controller')->defaultValue('Sylius\\Bundle\\ResourceBundle\\Controller\\ResourceController')->end()
                                ->scalarNode('repository')->defaultValue('Sylius\\Bundle\\ResourceBundle\\Doctrine\\ORM\\EntityRepository')->end()
                                ->scalarNode('form')->defaultValue('Sylius\\Bundle\\CustomizationBundle\\Form\\Type\\CustomizationType')->end()
                            ->end()
                        ->end()
                        ->arrayNode('customization_value')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Sylius\\Component\\Customization\\Model\\CustomizationValue')->end()
                                ->scalarNode('controller')->defaultValue('Sylius\\Bundle\\ResourceBundle\\Controller\\ResourceController')->end()
                                ->scalarNode('repository')->defaultValue('Sylius\\Bundle\\ResourceBundle\\Doctrine\\ORM\\EntityRepository')->end()
                                ->scalarNode('form')->defaultValue('Sylius\\Bundle\\CustomizationBundle\\Form\\Type\\CustomizationValueType')->end()
                            ->end()
                        ->end()
                        ->arrayNode('customization_subject')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Sylius\\Component\\Customization\\Model\\CustomizationSubject')->end()
                                ->scalarNode('controller')->defaultValue('Sylius\\Bundle\\ResourceBundle\\Controller\\ResourceController')->end()
                                ->scalarNode('repository')->defaultValue('Sylius\\Bundle\\ResourceBundle\\Doctrine\\ORM\\EntityRepository')->end()
                                ->scalarNode('form')->end()
                            ->end()
                        ->end()
                        ->arrayNode('customization_subject_instance')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Sylius\\Component\\Customization\\Model\\CustomizationSubjectInstance')->end()
                                ->scalarNode('controller')->defaultValue('Sylius\\Bundle\\ResourceBundle\\Controller\\ResourceController')->end()
                                ->scalarNode('repository')->defaultValue('Sylius\\Bundle\\ResourceBundle\\Doctrine\\ORM\\EntityRepository')->end()
                                ->scalarNode('form')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
