<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Bundle\ThemeBundle\Factory\ThemeFactory;
use Sylius\Bundle\ThemeBundle\Model\Theme;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sylius_theme');

        $this->addContextConfiguration($rootNode);
        $this->addResourcesConfiguration($rootNode);
        $this->addSourcesConfiguration($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function addResourcesConfiguration(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)->end()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('theme')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Theme::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(ThemeInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(ThemeFactory::class)->cannotBeEmpty()->end()
                                        ->arrayNode('form')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('choice')->defaultValue(ResourceChoiceType::class)->cannotBeEmpty()->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                                ->arrayNode('validation_groups')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->arrayNode('choice')
                                            ->prototype('scalar')->end()
                                            ->defaultValue(['sylius'])
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
        ;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function addSourcesConfiguration(ArrayNodeDefinition $rootNode)
    {
        $sourcesNodeBuilder = $rootNode
            ->addDefaultsIfNotSet()
            ->fixXmlConfig('source')
                ->children()
                    ->arrayNode('sources')
                        ->addDefaultsIfNotSet()
                            ->children()
        ;

        $sourcesNodeBuilder
            ->arrayNode('filesystem')
                ->addDefaultsIfNotSet()
                ->fixXmlConfig('location')
                    ->children()
                        ->arrayNode('locations')
                            ->requiresAtLeastOneElement()
                            ->performNoDeepMerging()
                            ->defaultValue(['%kernel.root_dir%/themes', '%kernel.root_dir%/../vendor/sylius/themes'])
                            ->prototype('scalar')
        ;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function addContextConfiguration(ArrayNodeDefinition $rootNode)
    {
        $rootNode->children()->scalarNode('context')->defaultValue('sylius.theme.context.settable')->cannotBeEmpty();
    }
}
