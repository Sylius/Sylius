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

        $driver = $config['driver'];
        $engine = $config['engine'];

        if (!in_array($driver, SyliusInventoryBundle::getSupportedDrivers())) {
            throw new \InvalidArgumentException(sprintf('Driver "%s" is unsupported for this extension', $driver));
        }

        $loader->load(sprintf('driver/%s.xml', $driver));
        $loader->load(sprintf('engine/%s.xml', $engine));

        $container->setParameter('sylius_inventory.driver', $driver);
        $container->setParameter('sylius_inventory.engine', $engine);

        $container->setParameter('sylius_inventory.backorders', $config['backorders']);

        $container->setAlias('sylius_inventory.checker', $config['checker']);
        $container->setAlias('sylius_inventory.operator', $config['operator']);

        $unitClasses = $config['classes']['unit'];

        $container->setParameter('sylius_inventory.controller.unit.class', $unitClasses['controller']);
        $container->setParameter('sylius_inventory.model.unit.class', $unitClasses['model']);

        if (array_key_exists('repository', $unitClasses)) {
            $container->setParameter('sylius_inventory.repository.unit.class', $unitClasses['repository']);
        }

        $stockableClasses = $config['classes']['stockable'];

        $container->setParameter('sylius_inventory.controller.stockable.class', $stockableClasses['controller']);
        $container->setParameter('sylius_inventory.model.stockable.class', $stockableClasses['model']);

        if (array_key_exists('repository', $stockableClasses)) {
            $container->setParameter('sylius_inventory.repository.stockable.class', $stockableClasses['repository']);
        }

        $loader->load('services.xml');
    }
}
