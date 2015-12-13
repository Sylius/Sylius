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

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Sylius\Component\Cart\Model\Cart;
use Sylius\Component\Cart\Model\CartItem;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Jérémy Leherpeur <jeremy@leherpeur.net>
 */
class SyliusCartExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $configFiles = array(
            'services.xml',
            'templating.xml',
            'twig.xml',
        );

        foreach ($configFiles as $configFile) {
            $loader->load($configFile);
        }

        $container->setAlias('sylius.cart_provider', $config['provider']);
        $container->setAlias('sylius.cart_resolver', $config['resolver']);

        $container->setAlias('sylius.repository.cart', 'sylius.repository.order');
        $container->setAlias('sylius.factory.cart', 'sylius.factory.order');
        $container->setAlias('sylius.manager.cart', 'sylius.manager.order');
        $container->setAlias('sylius.repository.cart_item', 'sylius.repository.order_item');
        $container->setAlias('sylius.factory.cart_item', 'sylius.factory.order_item');
        $container->setAlias('sylius.manager.cart_item', 'sylius.manager.order_item');

        $cartConfig = $config['resources']['cart'];
        $cartItemConfig = $config['resources']['cart_item'];

        $container->setParameter('sylius.controller.cart.class', $cartConfig['classes']['controller']);
        $container->setParameter('sylius.form.type.cart.class', $cartConfig['classes']['form']['default']);
        $container->setParameter('sylius.validation_groups.cart', $cartConfig['validation_groups']);
        $container->setParameter('sylius.controller.cart_item.class', $cartItemConfig['classes']['controller']);
        $container->setParameter('sylius.form.type.cart_item.class', $cartItemConfig['classes']['form']['default']);
        $container->setParameter('sylius.validation_groups.cart_item', $cartItemConfig['validation_groups']);

        $definition = $container->findDefinition('sylius.context.cart');
        $definition->replaceArgument(0, new Reference($config['storage']));
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        if (!$container->hasExtension('sylius_order')) {
            throw new \RuntimeException('Please install and configure SyliusOrderBundle in order to use SyliusCartBundle.');
        }

        $container->prependExtensionConfig('sylius_order', array(
            'resources' => array(
                'order' => array(
                    'classes' => array(
                        'model' => Cart::class,
                    ),
                ),
                'order_item' => array(
                    'classes' => array(
                        'model' => CartItem::class,
                    ),
                ),
            ))
        );
    }
}
