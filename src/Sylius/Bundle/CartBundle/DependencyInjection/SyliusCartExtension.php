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

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Carts extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 * @author Саша Стаменковић <umpirsky@gmail.com>
 * @author Jérémy Leherpeur <jeremy@leherpeur.net>
 */
class SyliusCartExtension extends Extension implements PrependExtensionInterface
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

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        if(!array_key_exists('SyliusSalesBundle', $container->getParameter('kernel.bundles')))
        {
            return;
        }

        $config = array('classes' => array(
            'order_item' => array(
                'model' => 'Sylius\Bundle\CartBundle\Model\CartItem'
            ),
            'order' => array(
                'model' => 'Sylius\Bundle\CartBundle\Model\Cart'
            )
        ));
        $container->prependExtensionConfig('sylius_sales', $config);
    }
}
