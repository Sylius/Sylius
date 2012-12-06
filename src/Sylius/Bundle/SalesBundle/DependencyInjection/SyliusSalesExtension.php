<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\DependencyInjection;

use Sylius\Bundle\SalesBundle\SyliusSalesBundle;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Sales extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusSalesExtension extends Extension
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

        if (!in_array($driver, SyliusSalesBundle::getSupportedDrivers())) {
            throw new \InvalidArgumentException(sprintf('Driver "%s" is unsupported for this extension.', $config['driver']));
        }

        $loader->load(sprintf('driver/%s.xml', $driver));

        $container->setParameter('sylius_sales.driver', $driver);
        $container->setParameter('sylius_sales.engine', $config['engine']);

        if (isset($config['builder'])) {
            $container->setAlias('sylius_sales.builder', $config['builder']);
        }

        $orderClasses = $config['classes']['order'];

        $container->setParameter('sylius_sales.model.order.class', $orderClasses['model']);
        $container->setParameter('sylius_sales.controller.order.class', $orderClasses['controller']);
        $container->setParameter('sylius_sales.form.type.order.class', $orderClasses['form']);

        if (isset($orderClasses['repository'])) {
            $container->setParameter('sylius_sales.repository.order.class', $orderClasses['repository']);
        }

        $itemClasses = $config['classes']['item'];

        $container->setParameter('sylius_sales.model.item.class', $itemClasses['model']);
        $container->setParameter('sylius_sales.controller.item.class', $itemClasses['controller']);
        $container->setParameter('sylius_sales.form.type.item.class', $itemClasses['form']);

        if (isset($itemClasses['repository'])) {
            $container->setParameter('sylius_sales.repository.item.class', $itemClasses['repository']);
        }

        $loader->load('services.xml');
    }
}
