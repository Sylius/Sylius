<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InventoryBundle\DependencyInjection;

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
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sylius_inventory');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)->end()
                ->booleanNode('backorders')->defaultTrue()->end()
                ->booleanNode('tracking')->defaultTrue()->end()
                ->arrayNode('events')->prototype('scalar')->end()
            ->end()
        ;

        $this->addServicesSection($rootNode);
        $this->addResourcesSection($rootNode);

        return $treeBuilder;
    }

    /**
     * Adds `services` section.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addServicesSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('services')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('checker')->defaultValue('sylius.availability_checker.default')->cannotBeEmpty()->end()
                        ->scalarNode('operator')->defaultValue('sylius.inventory_operator.default')->cannotBeEmpty()->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * Adds `resources` section.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addResourcesSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('resources')
                    ->isRequired()
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('stockable')
                            ->isRequired()
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arraynode('classes')
                                    ->adddefaultsifnotset()
                                    ->children()
                                        ->scalarnode('model')->isRequired()->end()
                                        ->scalarnode('interface')->defaultvalue('Sylius\Component\Inventory\Model\StockableInterface')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('inventory_unit')
                            ->adddefaultsifnotset()
                            ->children()
                                ->arraynode('classes')
                                    ->adddefaultsifnotset()
                                    ->children()
                                        ->scalarnode('model')->defaultvalue('Sylius\Component\Inventory\Model\InventoryUnit')->end()
                                        ->scalarnode('interface')->defaultvalue('Sylius\Component\Inventory\Model\InventoryUnitInterface')->end()
                                        ->scalarnode('controller')->defaultvalue('Sylius\Bundle\InventoryBundle\Controller\InventoryUnitController')->end()
                                        ->scalarnode('repository')->cannotbeempty()->end()
                                        ->scalarnode('factory')->cannotbeempty()->end()
                                    ->end()
                                ->end()
                                ->arrayNode('validation_groups')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->arrayNode('default')
                                            ->prototype('scalar')->end()
                                            ->defaultValue(array('sylius'))
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
