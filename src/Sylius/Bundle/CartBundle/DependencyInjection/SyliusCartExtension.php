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

        $container->setAlias('sylius.cart_provider', $config['provider']);
        $container->setAlias('sylius.cart_resolver', $config['resolver']);
        $container->setAlias('sylius.cart_storage', $config['storage']);

        $classes = $config['classes'];

        $container->setParameter('sylius.controller.cart.class', $classes['cart']['controller']);
        $container->setParameter('sylius.form.type.cart.class', $classes['cart']['form']);

        $container->setParameter('sylius.controller.cart_item.class', $classes['item']['controller']);
        $container->setParameter('sylius.form.type.cart_item.class', $classes['item']['form']);

        $container->setParameter('sylius.validation_group.cart', $config['validation_groups']['cart']);
        $container->setParameter('sylius.validation_group.cart_item', $config['validation_groups']['item']);

        $loader->load('services.xml');
        $loader->load('twig.xml');
    }
}
