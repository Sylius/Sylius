<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AttributeBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Attribute\Model\Attribute;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Attribute\Model\AttributeTranslation;
use Sylius\Component\Attribute\Model\AttributeTranslationInterface;
use Sylius\Component\Resource\Factory\Factory;
use Sylius\Component\Resource\Factory\TranslatableFactory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sylius_attribute');

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
                            ->scalarNode('subject')->isRequired()->end()
                            ->arrayNode('attribute')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->variableNode('options')->end()
                                    ->arrayNode('classes')
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->scalarNode('model')->defaultValue(Attribute::class)->cannotBeEmpty()->end()
                                            ->scalarNode('interface')->defaultValue(AttributeInterface::class)->cannotBeEmpty()->end()
                                            ->scalarNode('controller')->cannotBeEmpty()->end()
                                            ->scalarNode('repository')->cannotBeEmpty()->end()
                                            ->scalarNode('factory')->defaultValue(TranslatableFactory::class)->end()
                                            ->scalarNode('form')->cannotBeEmpty()->end()
                                        ->end()
                                    ->end()
                                    ->arrayNode('translation')
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->variableNode('options')->end()
                                            ->arrayNode('classes')
                                                ->addDefaultsIfNotSet()
                                                ->children()
                                                    ->scalarNode('model')->defaultValue(AttributeTranslation::class)->cannotBeEmpty()->end()
                                                    ->scalarNode('interface')->defaultValue(AttributeTranslationInterface::class)->cannotBeEmpty()->end()
                                                    ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                                    ->scalarNode('repository')->cannotBeEmpty()->end()
                                                    ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                                    ->scalarNode('form')->cannotBeEmpty()->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('attribute_value')
                                ->isRequired()
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->variableNode('options')->end()
                                    ->arrayNode('classes')
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->scalarNode('model')->isRequired()->cannotBeEmpty()->end()
                                            ->scalarNode('interface')->isRequired()->cannotBeEmpty()->end()
                                            ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                            ->scalarNode('repository')->cannotBeEmpty()->end()
                                            ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                            ->scalarNode('form')->cannotBeEmpty()->end()
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
