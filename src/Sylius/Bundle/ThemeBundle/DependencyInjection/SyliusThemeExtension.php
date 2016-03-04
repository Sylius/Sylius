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
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class SyliusThemeExtension extends AbstractResourceExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/services'));
        $loader->load('assets.xml');
        $loader->load('configuration.xml');
        $loader->load('resource_locators.xml');
        $loader->load('services.xml');
        $loader->load('templating.xml');
        $loader->load('translations.xml');

        $container->setAlias('sylius.context.theme', $config['context']);

        // TODO: Interfaces ready for filesystem decoupling, configuration not ready yet
        $loader->load('filesystem_configuration.xml');
        $container->setAlias('sylius.theme.configuration.loader', 'sylius.theme.configuration.loader.json_file');
        $container->setAlias('sylius.theme.configuration.provider', 'sylius.theme.configuration.provider.filesystem');
        $container->setParameter('sylius.theme.configuration.filesystem.locations', $config['sources']['filesystem']['locations']);

        $loader->load(sprintf('driver/%s.xml', $config['driver']));
        $this->registerResources('sylius', $config['driver'], $config['resources'], $container);

        $container->setParameter('sylius.interface.theme.class', $config['resources']['theme']['classes']['interface']);
    }
}
