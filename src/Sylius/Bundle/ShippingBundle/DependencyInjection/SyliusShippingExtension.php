<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\DependencyInjection;

use Sylius\Bundle\ShippingBundle\SyliusShippingBundle;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Sylius shipping component extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class SyliusShippingExtension extends Extension
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

        if (!in_array($driver, SyliusShippingBundle::getSupportedDrivers())) {
            throw new \InvalidArgumentException(sprintf('Driver "%s" is unsupported for SyliusShippingBundle', $driver));
        }

        $loader->load(sprintf('driver/%s.xml', $driver));

        $container->setParameter('sylius_shipping.driver', $driver);
        $container->setParameter('sylius_shipping.engine', $config['engine']);

        $classes = $config['classes'];

        $shipmentClasses = $classes['shipment'];

        $container->setParameter('sylius_shipping.model.shipment.class', $shipmentClasses['model']);

        if (isset($shipmentClasses['repository'])) {
            $container->setParameter('sylius_shipping.repository.shipment.class', $shipmentClasses['repository']);
        }

        $container->setParameter('sylius_shipping.controller.shipment.class', $shipmentClasses['controller']);
        $container->setParameter('sylius_shipping.form.type.shipment.class', $shipmentClasses['form']);

        $shipmentItemClasses = $classes['shipment_item'];

        $container->setParameter('sylius_shipping.model.shipment_item.class', $shipmentItemClasses['model']);

        if (isset($shipmentItemClasses['repository'])) {
            $container->setParameter('sylius_shipping.repository.shipment_item.class', $shipmentItemClasses['repository']);
        }

        $container->setParameter('sylius_shipping.controller.shipment_item.class', $shipmentItemClasses['controller']);
        $container->setParameter('sylius_shipping.form.type.shipment_item.class', $shipmentItemClasses['form']);

        $categoryClasses = $classes['category'];

        if (isset($categoryClasses['model'])) {
            $container->setParameter('sylius_shipping.model.category.class', $categoryClasses['model']);
        }

        if (isset($categoryClasses['repository'])) {
            $container->setParameter('sylius_shipping.repository.category.class', $categoryClasses['repository']);
        }

        $container->setParameter('sylius_shipping.controller.category.class', $categoryClasses['controller']);
        $container->setParameter('sylius_shipping.form.type.category.class', $categoryClasses['form']);

        $methodClasses = $classes['method'];

        if (isset($methodClasses['model'])) {
            $container->setParameter('sylius_shipping.model.method.class', $methodClasses['model']);
        }

        if (isset($methodClasses['repository'])) {
            $container->setParameter('sylius_shipping.repository.method.class', $methodClasses['repository']);
        }

        $container->setParameter('sylius_shipping.controller.method.class', $methodClasses['controller']);
        $container->setParameter('sylius_shipping.form.type.method.class', $methodClasses['form']);

        $loader->load('services.xml');
    }
}
