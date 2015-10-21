<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\StoreBundle\DependencyInjection;

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
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sylius_store');

        $rootNode
            ->children()
            ->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)->end()
            ->end();

        $this->addClassesSection($rootNode);

        return $treeBuilder;
    }

    private function addClassesSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
            ->arrayNode('classes')
            ->addDefaultsIfNotSet()
            ->children()
            ->arrayNode('store')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('model')->defaultValue('Sylius\Component\Store\Model\Store')->end()
            ->scalarNode('controller')->defaultValue('Sylius\Bundle\StoreBundle\Controller\StoreController')->end()
            ->scalarNode('repository')->end()
            ->arrayNode('form')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('default')->defaultValue('Sylius\Bundle\StoreBundle\Form\Type\StoreType')->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end();
    }
}
