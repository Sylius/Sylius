<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ArchetypeBundle\DependencyInjection;

use Sylius\Bundle\ArchetypeBundle\Form\Type\ArchetypeTranslationType;
use Sylius\Bundle\ArchetypeBundle\Form\Type\ArchetypeType;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Archetype\Model\Archetype;
use Sylius\Component\Archetype\Model\ArchetypeInterface;
use Sylius\Component\Archetype\Model\ArchetypeTranslation;
use Sylius\Component\Archetype\Model\ArchetypeTranslationInterface;
use Sylius\Component\Attribute\Model\Attribute;
use Sylius\Component\Resource\Factory\Factory;
use Sylius\Component\Resource\Factory\TranslatableFactory;
use Sylius\Component\Variation\Model\Option;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle.
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 *
 * @author Adam Elsodaney <adam.elso@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sylius_archetype');

        $rootNode
            ->addDefaultsIfNotSet()
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
        $node
            ->children()
                ->arrayNode('resources')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('subject')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('attribute')->isRequired()->defaultValue(Attribute::class)->cannotBeEmpty()->end()
                            ->scalarNode('option')->isRequired()->defaultValue(Option::class)->cannotBeEmpty()->end()
                            ->arrayNode('archetype')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->variableNode('options')->end()
                                    ->arrayNode('classes')
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->scalarNode('model')->defaultValue(Archetype::class)->cannotBeEmpty()->end()
                                            ->scalarNode('interface')->defaultValue(ArchetypeInterface::class)->cannotBeEmpty()->end()
                                            ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                            ->scalarNode('repository')->cannotBeEmpty()->end()
                                            ->scalarNode('factory')->defaultValue(TranslatableFactory::class)->end()
                                            ->arrayNode('form')
                                                ->addDefaultsIfNotSet()
                                                ->children()
                                                    ->scalarNode('default')->defaultValue(ArchetypeType::class)->cannotBeEmpty()->end()
                                                    ->scalarNode('choice')->defaultValue(ResourceChoiceType::class)->cannotBeEmpty()->end()
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
                                    ->arrayNode('translation')
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->variableNode('options')->end()
                                            ->arrayNode('classes')
                                                ->addDefaultsIfNotSet()
                                                ->children()
                                                    ->scalarNode('model')->defaultValue(ArchetypeTranslation::class)->cannotBeEmpty()->end()
                                                    ->scalarNode('interface')->defaultValue(ArchetypeTranslationInterface::class)->cannotBeEmpty()->end()
                                                    ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                                    ->scalarNode('repository')->cannotBeEmpty()->end()
                                                    ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                                    ->arrayNode('form')
                                                        ->addDefaultsIfNotSet()
                                                        ->children()
                                                            ->scalarNode('default')->defaultValue(ArchetypeTranslationType::class)->cannotBeEmpty()->end()
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
            ->end()
        ;
    }
}
