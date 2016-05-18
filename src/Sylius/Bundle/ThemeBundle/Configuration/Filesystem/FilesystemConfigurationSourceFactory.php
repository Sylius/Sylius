<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Configuration\Filesystem;

use Sylius\Bundle\ThemeBundle\Configuration\ConfigurationSourceFactoryInterface;
use Sylius\Bundle\ThemeBundle\Locator\RecursiveFileLocator;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class FilesystemConfigurationSourceFactory implements ConfigurationSourceFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildConfiguration(ArrayNodeDefinition $node)
    {
        $node
            ->fixXmlConfig('directory', 'directories')
                ->children()
                    ->scalarNode('filename')->defaultValue('composer.json')->cannotBeEmpty()->end()
                    ->arrayNode('directories')
                        ->defaultValue(['%kernel.root_dir%/themes'])
                        ->requiresAtLeastOneElement()
                        ->performNoDeepMerging()
                        ->prototype('scalar')
                    ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function initializeSource(ContainerBuilder $container, array $config)
    {
        $recursiveFileLocator = new Definition(RecursiveFileLocator::class, [
            new Reference('sylius.theme.finder_factory'),
            $config['directories'],
        ]);

        $configurationLoader = new Definition(ProcessingConfigurationLoader::class, [
            new Definition(JsonFileConfigurationLoader::class, [
                new Reference('sylius.theme.filesystem'),
            ]),
            new Reference('sylius.theme.configuration.processor'),
        ]);

        $configurationProvider = new Definition(FilesystemConfigurationProvider::class, [
            $recursiveFileLocator,
            $configurationLoader,
            $config['filename'],
        ]);

        return $configurationProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'filesystem';
    }
}
