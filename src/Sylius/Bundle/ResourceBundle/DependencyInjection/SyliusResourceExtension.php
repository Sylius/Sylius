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

use Sylius\Bundle\ResourceBundle\DependencyInjection\Factory\DatabaseDriverFactoryInterface;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Factory\ResourceServicesFactory;
use Symfony\Component\Config\Definition\ConfigurationInterface;
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
    private $factories = array();

    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $processor = new Processor();
        $config    = $processor->processConfiguration(new Configuration(), $config);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/container'));
        $loader->load('services.xml');

        if (isset($config['resources'])) {
            $this->createResourceServices($config['resources']);
        }
    }

    /**
     * Adds a factory that is able to handle a specific database driver type
     *
     * @param $factory
     */
    public function addDatabaseDriverFactory(DatabaseDriverFactoryInterface $factory)
    {
        $this->factories[$factory->getSupportedDriver()] = $factory;
    }

    /**
     * @param array $configs
     *
     * @throws \InvalidArgumentException
     */
    private function createResourceServices(array $configs)
    {
        foreach ($configs as $name => $config) {
            list($prefix, $resourceName) = explode('.', $name);

            $factory = $this->getFactoryForDriver($config['driver']);
            if (!$factory) {
                throw new \InvalidArgumentException(sprintf('Driver "%s" is unsupported, no factory exists for creating services', $config['driver']));
            }

            $factory->create($prefix, $resourceName, $config['classes'], array_key_exists('templates', $config) ? $config['templates'] : null);
        }
    }

    /**
     * @param $driver
     *
     * @return DatabaseDriverFactoryInterface
     */
    private function getFactoryForDriver($driver)
    {
        if (isset($this->factories[$driver])) {
            return $this->factories[$driver];
        }
    }
}
