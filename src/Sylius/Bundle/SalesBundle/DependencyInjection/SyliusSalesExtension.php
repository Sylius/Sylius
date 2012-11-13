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

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/container'));

        if (!in_array($config['driver'], SyliusSalesBundle::getSupportedDrivers())) {
            throw new \InvalidArgumentException(sprintf('Driver "%s" is unsupported for this extension.', $config['driver']));
        }

        $loader->load(sprintf('driver/%s.xml', $config['driver']));

        $container->setParameter('sylius_sales.driver', $config['driver']);
        $container->setParameter('sylius_sales.engine', $config['engine']);

        $container->setParameter('sylius_sales.model.order.class', $config['classes']['model']['order']);
        $container->setParameter('sylius_sales.model.item.class', $config['classes']['model']['item']);

        $container->setParameter('sylius_sales.controller.order.class', $config['classes']['controller']['order']);
        $container->setParameter('sylius_sales.controller.item.class', $config['classes']['controller']['item']);

        $container->setParameter('sylius_sales.form.type.order.class', $config['classes']['form']['type']['order']);
        $container->setParameter('sylius_sales.form.type.item.class', $config['classes']['form']['type']['item']);

        $loader->load('services.xml');
    }
}
