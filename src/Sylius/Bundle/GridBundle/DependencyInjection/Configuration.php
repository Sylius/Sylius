<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\GridBundle\DependencyInjection;

use Sylius\Bundle\GridBundle\Doctrine\ORM\Driver as DoctrineORMDriver;
use Sylius\Bundle\GridBundle\SyliusGridBundle;
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
        $rootNode = $treeBuilder->root('sylius_grid');

        $this->addDriversSection($rootNode);
        $this->addTemplatesSection($rootNode);
        $this->addGridsSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addDriversSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('drivers')
                    ->defaultValue([SyliusGridBundle::DRIVER_DOCTRINE_ORM])
                    ->prototype('enum')->values(SyliusGridBundle::getAvailableDrivers())->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addTemplatesSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('templates')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('filter')
                            ->useAttributeAsKey('name')
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('action')
                            ->useAttributeAsKey('name')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addGridsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('grids')
                    ->useAttributeAsKey('code')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('extends')->cannotBeEmpty()->end()
                            ->arrayNode('driver')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('name')->cannotBeEmpty()->defaultValue(DoctrineORMDriver::NAME)->end()
                                    ->arrayNode('options')
                                        ->prototype('variable')->end()
                                        ->defaultValue([])
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('sorting')
                                ->performNoDeepMerging()
                                ->useAttributeAsKey('name')
                                ->prototype('enum')->values(['asc', 'desc'])->cannotBeEmpty()->end()
                            ->end()
                            ->arrayNode('fields')
                                ->useAttributeAsKey('name')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('type')->isRequired()->cannotBeEmpty()->end()
                                        ->scalarNode('label')->cannotBeEmpty()->end()
                                        ->scalarNode('path')->cannotBeEmpty()->end()
                                        ->scalarNode('sortable')->end()
                                        ->scalarNode('enabled')->defaultTrue()->end()
                                        ->scalarNode('position')->defaultNull()->end()
                                        ->arrayNode('options')
                                            ->prototype('variable')->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('filters')
                                ->useAttributeAsKey('name')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('type')->isRequired()->cannotBeEmpty()->end()
                                        ->scalarNode('label')->cannotBeEmpty()->end()
                                        ->scalarNode('enabled')->defaultTrue()->end()
                                        ->scalarNode('template')->end()
                                        ->scalarNode('position')->defaultNull()->end()
                                        ->arrayNode('options')
                                            ->prototype('variable')->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('actions')
                                ->useAttributeAsKey('name')
                                ->prototype('array')
                                    ->useAttributeAsKey('name')
                                    ->prototype('array')
                                        ->children()
                                            ->scalarNode('type')->isRequired()->end()
                                            ->scalarNode('label')->end()
                                            ->scalarNode('icon')->end()
                                            ->arrayNode('options')
                                                ->prototype('variable')->end()
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
