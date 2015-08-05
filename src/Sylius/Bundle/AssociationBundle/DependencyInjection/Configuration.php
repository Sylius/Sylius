<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AssociationBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Łuksaz Chruściel <lukasz.chrusciel@lakion.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sylius_association');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)->end()
            ->end()
        ;

        $this->addClassesSection($rootNode);
        $this->addValidationGroupsSection($rootNode);
        $this->addAssociationSection($rootNode);

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
                        ->arrayNode('association_type')
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
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('association_type')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Sylius\Component\Association\Model\AssociationType')->end()
                                ->scalarNode('controller')->defaultValue('Sylius\Bundle\ResourceBundle\Controller\ResourceController')->end()
                                ->scalarNode('form')->defaultValue('Sylius\Bundle\AssociationBundle\Form\Type\AssociationTypeType')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
    ;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addAssociationSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('product_association')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->variableNode('classes')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
