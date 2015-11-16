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

use Sylius\Bundle\ReportBundle\DependencyInjection\Compiler\ServicesPass;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\DatabaseDriverFactory;
use Sylius\Component\Resource\Metadata\ResourceMetadata;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * Resource system extension.
 *
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
        $config    = $processor->processConfiguration(new Configuration(), $config);

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

        $resources = isset($config['resources']) ? $config['resources'] : array();
        $container->setParameter('sylius.resources', $config['resources']);

        foreach ($resources as $alias => $configuration) {
            $metadata = ResourceMetadata::fromConfigurationArray($alias, $configuration);

            DatabaseDriverFactory::getForResource($metadata)->load($container, $metadata);
        }
    }
}
