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

use Sylius\Bundle\InventoryBundle\Doctrine\ORM\StockItemRepository;
use Sylius\Bundle\InventoryBundle\Doctrine\ORM\StockLocationRepository;
use Sylius\Bundle\InventoryBundle\Doctrine\ORM\StockMovementRepository;
use Sylius\Bundle\InventoryBundle\Form\Type\StockItemType;
use Sylius\Bundle\InventoryBundle\Form\Type\StockLocationEntityType;
use Sylius\Bundle\InventoryBundle\Form\Type\StockLocationType;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\InventoryBundle\Controller\InventoryUnitController;
use Sylius\Component\Inventory\Factory\InventoryUnitFactory;
use Sylius\Component\Inventory\Factory\StockItemFactory;
use Sylius\Component\Inventory\Factory\StockMovementFactory;
use Sylius\Component\Inventory\Model\InventoryUnit;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Inventory\Model\StockItem;
use Sylius\Component\Inventory\Model\StockLocation;
use Sylius\Component\Inventory\Model\StockMovement;
use Sylius\Component\Resource\Factory\Factory;
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
                ->booleanNode('track_inventory')->defaultTrue()->end()
                ->scalarNode('checker')->defaultValue('sylius.availability_checker.default')->cannotBeEmpty()->end()
                ->scalarNode('operator')->cannotBeEmpty()->end()
                ->arrayNode('events')->prototype('scalar')->end()
            ->end()
        ->end()
        ->validate()
            ->ifTrue(function ($array) {
                return !isset($array['operator']);
            })
            ->then(function ($array) {
                $array['operator'] = 'sylius.inventory_operator.'.($array['track_inventory'] ? 'default' : 'noop');

                return $array;
            })
        ->end();

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
                    ->isRequired()
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('stockable')
                            ->isRequired()
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->isRequired()->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('stock_item')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(StockItem::class)->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->end()
                                        ->scalarNode('repository')->defaultValue(StockItemRepository::class)->end()
                                        ->scalarNode('factory')->defaultValue(StockItemFactory::class)->end()
                                        ->arrayNode('form')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('default')->defaultValue(StockItemType::class)->cannotBeEmpty()->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('stock_location')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(StockLocation::class)->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->end()
                                        ->scalarNode('repository')->defaultValue(StockLocationRepository::class)->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                        ->arrayNode('form')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('default')->defaultValue(StockLocationType::class)->cannotBeEmpty()->end()
                                                ->scalarNode('choice')->defaultValue(StockLocationEntityType::class)->cannotBeEmpty()->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('stock_movement')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(StockMovement::class)->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->end()
                                        ->scalarNode('repository')->defaultValue(StockMovementRepository::class)->end()
                                        ->scalarNode('factory')->defaultValue(StockMovementFactory::class)->end()
                                        ->arrayNode('form')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('default')->defaultValue('Sylius\Bundle\InventoryBundle\Form\Type\StockMovementType')->cannotBeEmpty()->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('inventory_unit')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(InventoryUnit::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(InventoryUnitInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(InventoryUnitController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(InventoryUnitFactory::class)->end()
                                        ->arrayNode('form')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('default')->defaultValue('Sylius\Bundle\InventoryBundle\Form\Type\StockMovementType')->cannotBeEmpty()->end()
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
