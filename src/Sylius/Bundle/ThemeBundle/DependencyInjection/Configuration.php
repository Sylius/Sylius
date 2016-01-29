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

        $sourcesNodeBuilder = $rootNode->addDefaultsIfNotSet()->fixXmlConfig('source')->children()->arrayNode('sources')->addDefaultsIfNotSet()->children();

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

        return $treeBuilder;
    }
}
