<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InventoryBundle\DependencyInjection;

use Sylius\Bundle\InventoryBundle\SyliusInventoryBundle;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Inventory dependency injection extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusInventoryExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        if (!in_array($config['engine'], array('twig'))) {
            throw new \InvalidArgumentException(sprintf('Engine "%s" is unsupported for this extension.', $config['engine']));
        }

        $loader->load(sprintf('engine/%s.xml', $config['engine']));

        $this->loadDriver($config['driver'], $config, $container, $loader);

        $loader->load('services.xml');

        $container->setParameter('sylius_inventory.driver', $config['driver']);
        $container->setParameter('sylius_inventory.engine', $config['engine']);

        $container->setParameter('sylius_inventory.backorders', $config['backorders']);
        $container->setParameter('sylius_inventory.model.iu.class', $config['classes']['model']['iu']);
    }

    /**
     * Load bundle driver.
     *
     * @param string           $driver
     * @param array            $config
     * @param ContainerBuilder $container
     * @param XmlFileLoader    $loader
     *
     * @throws InvalidArgumentException
     */
    protected function loadDriver($driver, array $config, ContainerBuilder $container, XmlFileLoader $loader)
    {
        if (!in_array($driver, SyliusInventoryBundle::getSupportedDrivers())) {
            throw new \InvalidArgumentException(sprintf('Driver "%s" is unsupported for this extension', $driver));
        }

        $loader->load(sprintf('driver/%s.xml', $driver));
    }

}
