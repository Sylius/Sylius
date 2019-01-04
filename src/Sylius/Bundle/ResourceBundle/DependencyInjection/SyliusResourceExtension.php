<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ResourceBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\Doctrine\DoctrineODMDriver;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\Doctrine\DoctrineORMDriver;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\Doctrine\DoctrinePHPCRDriver;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\DriverProvider;
use Sylius\Component\Resource\Metadata\Metadata;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SyliusResourceExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.xml');

        $bundles = $container->getParameter('kernel.bundles');
        if (array_key_exists('SyliusGridBundle', $bundles)) {
            $loader->load('services/integrations/grid.xml');
        }

        if ($config['translation']['enabled']) {
            $loader->load('services/integrations/translation.xml');

            $container->setAlias('sylius.translation_locale_provider', $config['translation']['locale_provider'])->setPublic(true);
        }

        $container->setParameter('sylius.resource.settings', $config['settings']);
        $container->setAlias('sylius.resource_controller.authorization_checker', $config['authorization_checker']);

        $this->loadPersistence($config['drivers'], $config['resources'], $loader);
        $this->loadResources($config['resources'], $container);

        $container->addObjectResource(Metadata::class);
        $container->addObjectResource(DriverProvider::class);
        $container->addObjectResource(DoctrineORMDriver::class);
        $container->addObjectResource(DoctrineODMDriver::class);
        $container->addObjectResource(DoctrinePHPCRDriver::class);
    }

    private function loadPersistence(array $drivers, array $resources, LoaderInterface $loader): void
    {
        foreach ($resources as $alias => $resource) {
            if (!in_array($resource['driver'], $drivers, true)) {
                throw new InvalidArgumentException(sprintf(
                    'Resource "%s" uses driver "%s", but this driver has not been enabled.',
                    $alias,
                    $resource['driver']
                ));
            }
        }

        foreach ($drivers as $driver) {
            $loader->load(sprintf('services/integrations/%s.xml', $driver));
        }
    }

    private function loadResources(array $resources, ContainerBuilder $container): void
    {
        foreach ($resources as $alias => $resourceConfig) {
            $metadata = Metadata::fromAliasAndConfiguration($alias, $resourceConfig);

            $resources = $container->hasParameter('sylius.resources') ? $container->getParameter('sylius.resources') : [];
            $resources = array_merge($resources, [$alias => $resourceConfig]);
            $container->setParameter('sylius.resources', $resources);

            DriverProvider::get($metadata)->load($container, $metadata);

            if ($metadata->hasParameter('translation')) {
                $alias .= '_translation';
                $resourceConfig = array_merge(['driver' => $resourceConfig['driver']], $resourceConfig['translation']);

                $resources = $container->hasParameter('sylius.resources') ? $container->getParameter('sylius.resources') : [];
                $resources = array_merge($resources, [$alias => $resourceConfig]);
                $container->setParameter('sylius.resources', $resources);

                $metadata = Metadata::fromAliasAndConfiguration($alias, $resourceConfig);

                DriverProvider::get($metadata)->load($container, $metadata);
            }
        }
    }
}
