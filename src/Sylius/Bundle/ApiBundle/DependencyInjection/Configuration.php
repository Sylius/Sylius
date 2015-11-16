<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ApiBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('sylius_api');

        $rootNode
            ->children()
                ->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)->end()
            ->end()
        ;

        $this->addResourcesSection($rootNode);

        return $treeBuilder;
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
                        ->arrayNode('api_user')
                            ->isRequired()
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->isRequired()
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->isRequired()->end()
                                        ->scalarNode('interface')->defaultValue('Sylius\Bundle\ApiBundle\Model\UserInterface')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('api_client')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Sylius\Bundle\ApiBundle\Model\Client')->end()
                                        ->scalarNode('interface')->defaultValue('Sylius\Bundle\ApiBundle\Model\ClientInterface')->end()
                                        ->scalarNode('controller')->defaultValue('Sylius\Bundle\ResourceBundle\Controller\ResourceController')->end()
                                        ->scalarNode('repository')->end()
                                        ->arrayNode('form')
                                            ->children()
                                                ->scalarNode('default')->defaultValue('Sylius\Bundle\ApiBundle\Form\Type\ClientType')->end()
                                            ->end()
                                        ->end()
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
                        ->arrayNode('api_access_token')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Sylius\Bundle\ApiBundle\Model\AccessToken')->end()
                                        ->scalarNode('interface')->defaultValue('Sylius\Bundle\ApiBundle\Model\AccessTokenInterface')->end()
                                        ->scalarNode('controller')->defaultValue('Sylius\Bundle\ResourceBundle\Controller\ResourceController')->end()
                                        ->scalarNode('repository')->end()
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
                        ->arrayNode('api_refresh_token')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Sylius\Bundle\ApiBundle\Model\RefreshToken')->end()
                                        ->scalarNode('interface')->defaultValue('Sylius\Bundle\ApiBundle\Model\RefreshTokenInterface')->end()
                                        ->scalarNode('controller')->defaultValue('Sylius\Bundle\ResourceBundle\Controller\ResourceController')->end()
                                        ->scalarNode('repository')->end()
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
                        ->arrayNode('api_auth_code')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Sylius\Bundle\ApiBundle\Model\AuthCode')->end()
                                        ->scalarNode('interface')->defaultValue('Sylius\Bundle\ApiBundle\Model\AuthCodeInterface')->end()
                                        ->scalarNode('controller')->defaultValue('Sylius\Bundle\ResourceBundle\Controller\ResourceController')->end()
                                        ->scalarNode('repository')->end()
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
