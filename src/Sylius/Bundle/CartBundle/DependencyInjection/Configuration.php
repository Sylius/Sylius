<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\DependencyInjection;

use Sylius\Bundle\CartBundle\Controller\CartController;
use Sylius\Bundle\CartBundle\Form\Type\CartType;
use Sylius\Bundle\CartBundle\Controller\CartItemController;
use Sylius\Bundle\CartBundle\Form\Type\CartItemType;
use Sylius\Component\Cart\Model\Cart;
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
        $rootNode = $treeBuilder->root('sylius_cart');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('provider')->defaultValue('sylius.cart_provider.default')->end()
                ->scalarNode('resolver')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('storage')->defaultValue('sylius.storage.session')->end()
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
                    ->isRequired()
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('cart')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('controller')->defaultValue(CartController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('form')->defaultValue(CartType::class)->cannotBeEmpty()->end()
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
                        ->arrayNode('item')
                            ->children()
                            ->arrayNode('classes')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('controller')->defaultValue(CartItemController::class)->cannotBeEmpty()->end()
                                    ->scalarNode('form')->defaultValue(CartItemType::class)->cannotBeEmpty()->end()
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
        ;
    }
}
