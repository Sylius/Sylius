<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\DependencyInjection;

use Sylius\Bundle\CartBundle\SyliusCartBundle;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Carts extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
class SyliusCartExtension extends Extension
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

        if (!in_array($config['driver'], SyliusCartBundle::getSupportedDrivers())) {
            throw new \InvalidArgumentException(sprintf('Driver "%s" is unsupported for this extension.', $config['driver']));
        }

        if ('twig' !== $config['engine']) {
            throw new \InvalidArgumentException(sprintf('Engine "%s" is unsupported for this extension.', $config['engine']));
        }

        $loader->load(sprintf('driver/%s.xml', $config['driver']));
        $loader->load(sprintf('engine/%s.xml', $config['engine']));

        $container->setParameter('sylius_cart.driver', $config['driver']);
        $container->setParameter('sylius_cart.engine', $config['engine']);

        $container->setAlias('sylius_cart.operator', $config['operator']);
        $container->setAlias('sylius_cart.resolver', $config['resolver']);
        $container->setAlias('sylius_cart.storage', $config['storage']);

        $container->setParameter('sylius_cart.provider.class', $config['classes']['provider']);

        $loader->load('services.xml');

        $container->setParameter('sylius_cart.model.cart.class', $config['classes']['model']['cart']);
        $container->setParameter('sylius_cart.model.item.class', $config['classes']['model']['item']);

        $container->setParameter('sylius_cart.controller.cart.class', $config['classes']['controller']['cart']);
        $container->setParameter('sylius_cart.controller.item.class', $config['classes']['controller']['item']);

        $container->setParameter('sylius_cart.form.type.cart.class', $config['classes']['form']['type']['cart']);
        $container->setParameter('sylius_cart.form.type.item.class', $config['classes']['form']['type']['item']);
    }
}
