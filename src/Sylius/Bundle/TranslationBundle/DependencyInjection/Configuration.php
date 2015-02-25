<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TranslationBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sylius_translation');

        $rootNode
            ->children()
                ->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)->end()
                ->scalarNode('locale_provider')->defaultValue('sylius.translation.locale_provider.request')->end()
            ->end()
        ;

        $this->addMappingDefaults($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addMappingDefaults(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('mapping')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('translatable')
                            ->children()
                                ->scalarNode('translations')->defaultValue('translations')->end()
                                ->scalarNode('current_locale')->defaultValue('currentLocale')->end()
                                ->scalarNode('fallback_locale')->defaultValue('fallbackLocale')->end()
                            ->end()
                        ->end()
                        ->arrayNode('translation')
                            ->children()
                                ->scalarNode('translatable')->defaultValue('translatable')->end()
                                ->scalarNode('locale')->defaultValue('locale')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
