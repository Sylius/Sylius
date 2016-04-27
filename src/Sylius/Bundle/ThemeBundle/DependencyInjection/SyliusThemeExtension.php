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

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class SyliusThemeExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/services'));
        $loader->load('services.xml');
        $loader->load(sprintf('driver/%s.xml', $config['driver']));

        $this->loadFilesystemConfiguration($container, $loader, $config);

        $container->setParameter('sylius.interface.theme.class', $config['resources']['theme']['classes']['interface']);
        $container->setAlias('sylius.context.theme', $config['context']);

        $this->registerResources('sylius', $config['driver'], $config['resources'], $container);

        $container->getDefinition('sylius.repository.theme')->setLazy(true);
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/services'));

        $this->prependSyliusSettings($container, $loader);
    }

    /**
     * @param ContainerBuilder $container
     * @param LoaderInterface $loader
     * @param array $config
     */
    private function loadFilesystemConfiguration(ContainerBuilder $container, LoaderInterface $loader, array $config)
    {
        $loader->load('configuration/filesystem.xml');

        $container->setAlias('sylius.theme.configuration.loader', 'sylius.theme.configuration.loader.json_file');
        $container->setAlias('sylius.theme.configuration.provider', 'sylius.theme.configuration.provider.filesystem');
        $container->setParameter('sylius.theme.configuration.filesystem.locations', $config['sources']['filesystem']['locations']);
    }

    /**
     * @param ContainerBuilder $container
     * @param LoaderInterface $loader
     */
    private function prependSyliusSettings(ContainerBuilder $container, LoaderInterface $loader)
    {
        if (!$container->hasExtension('sylius_settings')) {
            return;
        }

        $loader->load('integration/settings.xml');
    }
}
