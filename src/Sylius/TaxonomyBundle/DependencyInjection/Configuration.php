<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\TaxonomyBundle\DependencyInjection;

use Sylius\ResourceBundle\Controller\ResourceController;
use Sylius\ResourceBundle\Form\Type\ResourceFromIdentifierType;
use Sylius\ResourceBundle\Form\Type\ResourceToHiddenIdentifierType;
use Sylius\ResourceBundle\Form\Type\ResourceToIdentifierType;
use Sylius\ResourceBundle\SyliusResourceBundle;
use Sylius\TaxonomyBundle\Form\Type\TaxonTranslationType;
use Sylius\TaxonomyBundle\Form\Type\TaxonType;
use Sylius\Resource\Factory\Factory;
use Sylius\Resource\Factory\TranslatableFactory;
use Sylius\Taxonomy\Model\Taxon;
use Sylius\Taxonomy\Model\TaxonInterface;
use Sylius\Taxonomy\Model\TaxonTranslation;
use Sylius\Taxonomy\Model\TaxonTranslationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle.
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sylius_taxonomy');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)->end()
            ->end();

        $this->addResourcesSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addResourcesSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('taxon')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Taxon::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(TaxonInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(TranslatableFactory::class)->end()
                                        ->arrayNode('form')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('default')->defaultValue(TaxonType::class)->cannotBeEmpty()->end()
                                                ->scalarNode('from_identifier')->defaultValue(ResourceFromIdentifierType::class)->cannotBeEmpty()->end()
                                                ->scalarNode('to_identifier')->defaultValue(ResourceToIdentifierType::class)->cannotBeEmpty()->end()
                                                ->scalarNode('to_hidden_identifier')->defaultValue(ResourceToHiddenIdentifierType::class)->cannotBeEmpty()->end()
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
                                        ->arrayNode('from_identifier')
                                            ->prototype('scalar')->end()
                                            ->defaultValue(['sylius'])
                                        ->end()
                                            ->arrayNode('to_identifier')
                                            ->prototype('scalar')->end()
                                        ->defaultValue(['sylius'])
                                        ->end()
                                            ->arrayNode('to_hidden_identifier')
                                            ->prototype('scalar')->end()
                                        ->defaultValue(['sylius'])
                                        ->end()
                                    ->end()
                                ->end()
                                ->arrayNode('translation')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->variableNode('options')->end()
                                        ->arrayNode('classes')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->defaultValue(TaxonTranslation::class)->cannotBeEmpty()->end()
                                                ->scalarNode('interface')->defaultValue(TaxonTranslationInterface::class)->cannotBeEmpty()->end()
                                                ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                                ->scalarNode('repository')->cannotBeEmpty()->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                                ->arrayNode('form')
                                                    ->addDefaultsIfNotSet()
                                                    ->children()
                                                        ->scalarNode('default')->defaultValue(TaxonTranslationType::class)->cannotBeEmpty()->end()
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
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
