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

use Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\DriverProvider;
use Sylius\Component\Resource\Metadata\Metadata;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class SyliusResourceExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration($config, $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        
        $configFiles = [
            'services.xml',
            'controller.xml',
            'storage.xml',
            'routing.xml',
            'twig.xml',
        ];

        foreach ($configFiles as $configFile) {
            $loader->load($configFile);
        }

        $bundles = $container->getParameter('kernel.bundles');
        if (array_key_exists('SyliusGridBundle', $bundles)) {
            $loader->load('grid.xml');
        }

        if ($config['translation']['enabled']) {
            $loader->load('translation.xml');

            $container->setParameter('sylius.translation.default_locale', $config['translation']['default_locale']);
            $container->setAlias('sylius.translation.locale_provider', $config['translation']['locale_provider']);
            $container->setAlias('sylius.translation.available_locales_provider', $config['translation']['available_locales_provider']);
            $container->setParameter('sylius.translation.available_locales', $config['translation']['available_locales']);
        }

        foreach ($config['resources'] as $alias => $resourceConfig) {
            $metadata = Metadata::fromAliasAndConfiguration($alias, $resourceConfig);

            $resources = $container->hasParameter('sylius.resources') ? $container->getParameter('sylius.resources') : [];
            $resources = array_merge($resources, [$alias => $resourceConfig]);
            $container->setParameter('sylius.resources', $resources);

            DriverProvider::get($metadata)->load($container, $metadata);

            if ($metadata->hasParameter('translation')) {
                $alias = $alias.'_translation';
                $resourceConfig = array_merge(['driver' => $resourceConfig['driver']], $resourceConfig['translation']);

                $resources = $container->hasParameter('sylius.resources') ? $container->getParameter('sylius.resources') : [];
                $resources = array_merge($resources, [$alias => $resourceConfig]);
                $container->setParameter('sylius.resources', $resources);

                $metadata = Metadata::fromAliasAndConfiguration($alias, $resourceConfig);

                DriverProvider::get($metadata)->load($container, $metadata);
            }
        }

        $container->setParameter('sylius.resource.settings', $config['settings']);
        $container->setAlias('sylius.resource_controller.authorization_checker', $config['authorization_checker']);
    }
}
