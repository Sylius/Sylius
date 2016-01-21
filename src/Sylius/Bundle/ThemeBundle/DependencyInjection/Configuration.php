<?php

namespace Sylius\Bundle\ThemeBundle\DependencyInjection;

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

        $rootNode
            ->addDefaultsIfNotSet()
            ->fixXmlConfig('location')
                ->children()
                    ->arrayNode('locations')
                        ->requiresAtLeastOneElement()
                        ->performNoDeepMerging()
                        ->defaultValue(['%kernel.root_dir%/themes'])
                        ->prototype('scalar')
        ;

        return $treeBuilder;
    }
}
