<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sylius_core');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('driver')->cannotBeOverwritten()->isRequired()->cannotBeEmpty()->end()
                ->arrayNode('from_email')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('address')->defaultValue('webmaster@example.com')->cannotBeEmpty()->end()
                        ->scalarNode('sender_name')->defaultValue('webmaster')->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->arrayNode('order_confirmation')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->booleanNode('enabled')->defaultFalse()->end()
                        ->scalarNode('template')->defaultValue('SyliusWebBundle:Frontend/Email:orderConfirmation.html.twig')->end()
                        ->arrayNode('from_email')
                            ->canBeUnset()
                            ->children()
                                ->scalarNode('address')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('sender_name')->isRequired()->cannotBeEmpty()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        $this->addClassesSection($rootNode);

        return $treeBuilder;
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
                        ->arrayNode('user')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Sylius\\Bundle\\CoreBundle\\Model\\User')->end()
                                ->scalarNode('form')->defaultValue('Sylius\\Bundle\\CoreBundle\\Form\\Type\\UserType')->end()
                            ->end()
                        ->end()
                        ->arrayNode('group')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Sylius\\Bundle\\CoreBundle\\Model\\Group')->end()
                                ->scalarNode('form')->defaultValue('Sylius\\Bundle\\CoreBundle\\Form\\Type\\GroupType')->end()
                            ->end()
                        ->end()
                        ->arrayNode('variant_image')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Sylius\\Bundle\\CoreBundle\\Model\\VariantImage')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
