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
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), $config);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $configFiles = array(
            'services.xml',
            'storage.xml',
            'routing.xml',
            'twig.xml'
        );

        foreach ($configFiles as $configFile) {
            $loader->load($configFile);
        }

        foreach ($config['resources'] as $alias => $resourceConfig) {
            $metadata = Metadata::fromAliasAndConfiguration($alias, $resourceConfig);

            $resources = $container->hasParameter('sylius.resources') ? $container->getParameter('sylius.resources') : array();
            $resources = array_merge($resources, array($alias => $resourceConfig));
            $container->setParameter('sylius.resources', $resources);

            DriverProvider::get($metadata)->load($container, $metadata);

            if (isset($resourceConfig['translation']) && class_exists('Sylius\Bundle\TranslationBundle\SyliusTranslationBundle')) {
                $alias = $alias.'_translation';
                $resourceConfig = array_merge(array('driver' => $resourceConfig['driver']), $resourceConfig['translation']);

                $resources = $container->hasParameter('sylius.resources') ? $container->getParameter('sylius.resources') : array();
                $resources = array_merge($resources, array($alias => $resourceConfig));
                $container->setParameter('sylius.resources', $resources);

                $metadata = Metadata::fromAliasAndConfiguration($alias, $resourceConfig);

                DriverProvider::get($metadata)->load($container, $metadata);
            }
        }

        $container->setParameter('sylius.resource.settings', $config['settings']);
    }
}
