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
                            ->arrayNode('driver')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('name')->defaultValue(DoctrineORMDriver::NAME)->end()
                                    ->arrayNode('options')
                                        ->prototype('variable')->end()
                                        ->defaultValue([])
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('sorting')
                                ->useAttributeAsKey('name')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('path')->isRequired()->cannotBeEmpty()->end()
                                        ->enumNode('direction')->values(['asc', 'desc'])->defaultValue('asc')->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('fields')
                                ->useAttributeAsKey('name')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('type')->isRequired()->cannotBeEmpty()->end()
                                        ->scalarNode('label')->cannotBeEmpty()->end()
                                        ->scalarNode('path')->cannotBeEmpty()->end()
                                        ->scalarNode('enabled')->defaultTrue()->end()
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
