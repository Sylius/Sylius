<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MetadataBundle\DependencyInjection;

use Sylius\Bundle\MetadataBundle\Controller\MetadataController;
use Sylius\Bundle\MetadataBundle\Form\Type\MetadataContainerType;
use Sylius\Bundle\MetadataBundle\Model\MetadataContainer;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Metadata\Factory\MetadataContainerFactory;
use Sylius\Component\Metadata\Model\MetadataContainerInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle.
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 *
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
        $rootNode = $treeBuilder->root('sylius_metadata');

        $rootNode
            ->children()
                ->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)->end()
            ->end()
        ;

        $this->addResourcesSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addResourcesSection(ArrayNodeDefinition $node)
    {
        $resourcesBuilder = $node
            ->fixXmlConfig('resource')
            ->children()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()
        ;

        $resourcesBuilder
            ->arrayNode('metadata_container')
                ->addDefaultsIfNotSet()
                ->children()
                    ->variableNode('options')->end()
                    ->arrayNode('classes')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('model')->defaultValue(MetadataContainer::class)->cannotBeEmpty()->end()
                            ->scalarNode('interface')->defaultValue(MetadataContainerInterface::class)->cannotBeEmpty()->end()
                            ->scalarNode('controller')->defaultValue(MetadataController::class)->cannotBeEmpty()->end()
                            ->scalarNode('repository')->cannotBeEmpty()->end()
                            ->scalarNode('factory')->defaultValue(MetadataContainerFactory::class)->end()
                            ->arrayNode('form')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('default')->defaultValue(MetadataContainerType::class)->cannotBeEmpty()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('validation_groups')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->arrayNode('default')
                                ->prototype('scalar')->end()
                                ->defaultValue(['sylius'])
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
