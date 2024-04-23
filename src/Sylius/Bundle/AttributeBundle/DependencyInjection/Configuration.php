<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

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

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('sylius_attribute');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)->end()
            ->end()
        ;

        $this->addResourcesSection($rootNode);

        return $treeBuilder;
    }

    private function addResourcesSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('resources')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('subject')->isRequired()->end()
                            ->arrayNode('attribute')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->variableNode('options')
                                        ->setDeprecated('sylius/attribute-bundle', '1.13', 'The "%node%" node at "%path%" is deprecated and will be removed in 2.0.')
                                    ->end()
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
                                            ->variableNode('options')
                                                ->setDeprecated('sylius/attribute-bundle', '1.13', 'The "%node%" node at "%path%" is deprecated and will be removed in 2.0.')
                                            ->end()
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
                                    ->variableNode('options')
                                        ->setDeprecated('sylius/attribute-bundle', '1.13', 'The "%node%" node at "%path%" is deprecated and will be removed in 2.0.')
                                    ->end()
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
