<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxonomyBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
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
                        ->arrayNode('taxonomy')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius'))
                        ->end()
                        ->arrayNode('taxonomy_translation')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius'))
                        ->end()
                        ->arrayNode('taxon')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius'))
                        ->end()
                        ->arrayNode('taxon_translation')
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
                    ->isRequired()
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('taxonomy')
                            ->isRequired()
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Sylius\Component\Taxonomy\Model\Taxonomy')->end()
                                ->scalarNode('controller')->defaultValue('Sylius\Bundle\ResourceBundle\Controller\ResourceController')->end()
                                ->scalarNode('repository')->end()
                                ->arrayNode('form')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('default')->defaultValue('Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonomyType')->end()
                                        ->scalarNode('choice')->defaultValue('Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType')->end()
                                    ->end()
                                ->end()
                                ->arrayNode('translation')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Sylius\Component\Taxonomy\Model\TaxonomyTranslation')->end()
                                        ->scalarNode('controller')->defaultValue('Sylius\Bundle\ResourceBundle\Controller\ResourceController')->end()
                                        ->scalarNode('repository')->end()
                                        ->arrayNode('form')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('default')->defaultValue('Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonomyTranslationType')->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('mapping')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->arrayNode('fields')
                                                    ->prototype('scalar')->end()
                                                    ->defaultValue(array('name'))
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('taxon')
                            ->isRequired()
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Sylius\Component\Taxonomy\Model\Taxon')->end()
                                ->scalarNode('controller')->defaultValue('Sylius\Bundle\TaxonomyBundle\Controller\TaxonController')->end()
                                ->scalarNode('repository')->end()
                                ->arrayNode('form')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('default')->defaultValue('Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonType')->end()
                                    ->end()
                                ->end()
                                ->arrayNode('translation')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Sylius\Component\Taxonomy\Model\TaxonTranslation')->end()
                                        ->scalarNode('controller')->defaultValue('Sylius\Bundle\ResourceBundle\Controller\ResourceController')->end()
                                        ->scalarNode('repository')->end()
                                        ->arrayNode('form')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('default')->defaultValue('Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonTranslationType')->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('mapping')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->arrayNode('fields')
                                                    ->prototype('scalar')->end()
                                                    ->defaultValue(array('name', 'slug', 'permalink', 'description'))
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
