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
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $driver = $config['driver'];
        $engine = $config['engine'];

        if (!in_array($driver, SyliusCartBundle::getSupportedDrivers())) {
            throw new \InvalidArgumentException(sprintf('Driver "%s" is unsupported by SyliusCartBundle', $config['driver']));
        }

        if ('twig' !== $engine) {
            throw new \InvalidArgumentException(sprintf('Templating engine "%s" is unsupported for by SyliusCartBundle', $config['engine']));
        }

        $container->setParameter('sylius_cart.driver', $driver);

        $loader->load(sprintf('driver/%s.xml', $driver));
        $loader->load(sprintf('engine/%s.xml', $engine));

        $container->setParameter('sylius.engine', $engine);

        $container->setAlias('sylius.cart_provider', $config['provider']);
        $container->setAlias('sylius.cart_resolver', $config['resolver']);
        $container->setAlias('sylius.cart_storage', $config['storage']);

        $classes = $config['classes'];

        $cartClasses = $classes['cart'];
        $cartItemClasses = $classes['item'];

        $container->setParameter('sylius.controller.cart.class', $cartClasses['controller']);
        $container->setParameter('sylius.form.type.cart.class', $cartClasses['form']);

        if (isset($cartClasses['model'])) {
            $container->setParameter('sylius.model.cart.class', $cartClasses['model']);
        }
        if (isset($cartClasses['repository'])) {
            $container->setParameter('sylius.repository.cart.class', $cartClasses['repository']);
        }

        $container->setParameter('sylius.model.cart_item.class', $cartItemClasses['model']);
        $container->setParameter('sylius.controller.cart_item.class', $cartItemClasses['controller']);
        $container->setParameter('sylius.form.type.cart_item.class', $cartItemClasses['form']);

        if (isset($cartItemClasses['repository'])) {
            $container->setParameter('sylius.repository.cart_item.class', $cartItemClasses['repository']);
        }

        $container->setParameter('sylius.validation_group.cart', $config['validation_groups']['cart']);
        $container->setParameter('sylius.validation_group.cart_item', $config['validation_groups']['item']);

        $loader->load('services.xml');
    }
}
