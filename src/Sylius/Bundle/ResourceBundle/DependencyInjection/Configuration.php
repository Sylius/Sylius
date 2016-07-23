<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
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
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sylius_resource');

        $this->addResourcesSection($rootNode);
        $this->addSettingsSection($rootNode);
        $this->addTranslationsSection($rootNode);
        $this->addDriversSection($rootNode);

        $rootNode
            ->children()
                ->scalarNode('authorization_checker')
                    ->defaultValue('sylius.resource_controller.authorization_checker.disabled')
                    ->cannotBeEmpty()
                ->end()
            ->end()
        ;

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
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)->end()
                            ->variableNode('options')->end()
                            ->scalarNode('templates')->cannotBeEmpty()->end()
                            ->arrayNode('classes')
                                ->isRequired()
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('model')->isRequired()->cannotBeEmpty()->end()
                                    ->scalarNode('interface')->cannotBeEmpty()->end()
                                    ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                    ->scalarNode('repository')->cannotBeEmpty()->end()
                                    ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                    ->arrayNode('form')
                                        ->prototype('scalar')->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('validation_groups')
                                ->useAttributeAsKey('name')
                                ->prototype('array')
                                    ->prototype('scalar')->end()
                                    ->defaultValue([])
                                ->end()
                            ->end()
                            ->arrayNode('translation')
                                ->children()
                                    ->variableNode('options')->end()
                                    ->arrayNode('classes')
                                        ->isRequired()
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->scalarNode('model')->isRequired()->cannotBeEmpty()->end()
                                            ->scalarNode('interface')->cannotBeEmpty()->end()
                                            ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                            ->scalarNode('repository')->cannotBeEmpty()->end()
                                            ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                            ->arrayNode('form')
                                                ->prototype('scalar')->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                    ->arrayNode('validation_groups')
                                        ->useAttributeAsKey('name')
                                        ->prototype('array')
                                            ->prototype('scalar')->end()
                                            ->defaultValue([])
                                        ->end()
                                    ->end()
                                    ->arrayNode('fields')
                                        ->prototype('scalar')->end()
                                        ->defaultValue([])
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param $node
     */
    private function addSettingsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('settings')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->variableNode('paginate')->defaultNull()->end()
                        ->variableNode('limit')->defaultNull()->end()
                        ->arrayNode('allowed_paginate')
                            ->prototype('integer')->end()
                            ->defaultValue([10, 20, 30])
                        ->end()
                        ->integerNode('default_page_size')->defaultValue(10)->end()
                        ->booleanNode('sortable')->defaultFalse()->end()
                        ->variableNode('sorting')->defaultNull()->end()
                        ->booleanNode('filterable')->defaultFalse()->end()
                        ->variableNode('criteria')->defaultNull()->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addTranslationsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('translation')
                    ->canBeEnabled()
                    ->children()
                        ->scalarNode('default_locale')->cannotBeEmpty()->end()
                        ->scalarNode('locale_provider')->defaultValue('sylius.translation.locale_provider.request')->cannotBeEmpty()->end()
                        ->scalarNode('available_locales_provider')->defaultValue('sylius.translation.locales_provider.array')->cannotBeEmpty()->end()
                        ->arrayNode('available_locales') ->prototype('scalar')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addDriversSection(ArrayNodeDefinition $node)
    {
        // determine which drivers are distributed with this bundle
        $driverDir = __DIR__ . '/../Resources/config/driver';
        $iterator = new \RecursiveDirectoryIterator($driverDir);
        foreach (new \RecursiveIteratorIterator($iterator) as $file) {
            if ($file->getExtension() !== 'xml') {
                continue;
            }

            // we use the parent directory name in addition to the filename to
            // determine the name of the driver (e.g. doctrine/orm)
            $validDrivers[] = str_replace('\\','/',substr($file->getPathname(), 1 + strlen($driverDir), -4));
        }

        $node
            ->children()
                ->arrayNode('drivers')
                    ->info('Enable drivers which are distributed with this bundle')
                    ->validate()
                    ->ifTrue(function ($value) use ($validDrivers) {
                        return 0 !== count(array_diff($value, $validDrivers));
                    })
                        ->thenInvalid(sprintf('Invalid driver specified in %%s, valid drivers: ["%s"]', implode('", "', $validDrivers)))
                    ->end()
                    ->defaultValue(['doctrine/orm'])
                    ->prototype('scalar')->end()
                ->end()
            ->end();
    }
}
