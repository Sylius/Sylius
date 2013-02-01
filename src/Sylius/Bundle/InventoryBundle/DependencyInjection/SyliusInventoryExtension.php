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
 * @author Саша Стаменковић <umpirsky@gmail.com>
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

        $container->setParameter('sylius.backorders', $config['backorders']);

        $container->setAlias('sylius.availability_checker', $config['checker']);
        $container->setAlias('sylius.inventory_operator', $config['operator']);

        $unitClasses = $config['classes']['unit'];

        $container->setParameter('sylius.controller.inventory_unit.class', $unitClasses['controller']);
        $container->setParameter('sylius.model.inventory_unit.class', $unitClasses['model']);

        if (array_key_exists('repository', $unitClasses)) {
            $container->setParameter('sylius.repository.inventory_unit.class', $unitClasses['repository']);
        }

        $stockableClasses = $config['classes']['stockable'];

        $container->setParameter('sylius.controller.stockable.class', $stockableClasses['controller']);
        $container->setParameter('sylius.model.stockable.class', $stockableClasses['model']);

        if (array_key_exists('repository', $stockableClasses)) {
            $container->setParameter('sylius.repository.stockable.class', $stockableClasses['repository']);
        }

        $loader->load('services.xml');

        $listenerDefinition = $container->getDefinition('sylius.inventory_listener');
        if (isset($config['events'])) {
            foreach ($config['events'] as $event) {
                $listenerDefinition->addTag(
                    'kernel.event_listener',
                    array('event' => $event, 'method' => 'onInventoryChange')
                );
            }
        }
    }
}
