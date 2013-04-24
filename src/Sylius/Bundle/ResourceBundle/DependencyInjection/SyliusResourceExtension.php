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

use Sylius\Bundle\ResourceBundle\DependencyInjection\Factory\ResourceServicesFactory;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Resource system extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class SyliusResourceExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $config);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/container'));
        $loader->load('services.xml');

        if (isset($config['resources'])) {
            $this->createResourceServices($config['resources'], $container);
        }
    }

    private function createResourceServices(array $configs, ContainerBuilder $container)
    {
        $factory = new ResourceServicesFactory($container);

        foreach ($configs as $name => $config) {
            list($prefix, $resourceName) = explode('.', $name);

            $templates = array_key_exists('templates', $config) ? $config['templates'] : null;

            $factory->create($prefix, $resourceName, $config['driver'], $config['classes'], $templates);
        }
    }
}
