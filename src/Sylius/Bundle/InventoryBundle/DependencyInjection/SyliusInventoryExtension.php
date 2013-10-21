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

use Sylius\Bundle\ResourceBundle\DependencyInjection\SyliusResourceExtension;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * Inventory dependency injection extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
class SyliusInventoryExtension extends SyliusResourceExtension
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

        $driver = $config['driver'];

        $this->loadDatabaseDriver($driver, $loader, $container);

        $container->setParameter('sylius.backorders', $config['backorders']);

        $container->setAlias('sylius.availability_checker', $config['checker']);
        $container->setAlias('sylius.inventory_operator', $config['operator']);

        $classes = $config['classes'];

        $container->setParameter('sylius.controller.inventory_unit.class', $classes['unit']['controller']);
        $container->setParameter('sylius.model.inventory_unit.class', $classes['unit']['model']);

        if (array_key_exists('repository', $classes['unit'])) {
            $container->setParameter('sylius.repository.inventory_unit.class', $classes['unit']['repository']);
        }

        $container->setParameter('sylius.model.stockable.class', $classes['stockable']['model']);

        $loader->load('services.xml');
        $loader->load('twig.xml');

        $listenerDefinition = $container->getDefinition('sylius.inventory_listener');

        if (isset($config['events'])) {
            foreach ($config['events'] as $event) {
                $listenerDefinition->addTag('kernel.event_listener', array('event' => $event, 'method' => 'onInventoryChange'));
            }
        }

        if ($container->hasParameter('sylius.config.classes')) {
            $classes = array_merge($classes, $container->getParameter('sylius.config.classes'));
        }

        $container->setParameter('sylius.config.classes', $classes);
    }
}
