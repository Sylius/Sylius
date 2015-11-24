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
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Carts extension.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Jérémy Leherpeur <jeremy@leherpeur.net>
 */
class SyliusCartExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    protected $configFiles = array(
        'services.xml',
        'templating.xml',
        'twig.xml',
    );

    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->configure($config, new Configuration(), $container);

        $container->setAlias('sylius.cart_provider', $config['provider']);
        $container->setAlias('sylius.cart_resolver', $config['resolver']);

        $definition = $container->findDefinition('sylius.context.cart');
        $definition->replaceArgument(0, new Reference($config['storage']));

        $resources = $config['resources'];

        $container->setParameter('sylius.controller.cart.class', $resources['cart']['classes']['controller']);
        $container->setParameter('sylius.form.type.cart.class', $resources['cart']['classes']['form']);

        $container->setParameter('sylius.controller.cart_item.class', $resources['item']['classes']['controller']);
        $container->setParameter('sylius.form.type.cart_item.class', $resources['item']['classes']['form']);

        $container->setParameter('sylius.validation_group.cart', $resources['cart']['validation_groups']);
        $container->setParameter('sylius.validation_group.cart_item', $resources['item']['validation_groups']);
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        if (!$container->hasExtension('sylius_order')) {
            return;
        }

        $container->prependExtensionConfig('sylius_order', array(
            'resources' => array(
                'order_item' => array(
                    'classes' => array(
                        'model' => 'Sylius\Component\Cart\Model\CartItem'
                    )
                ),
                'order' => array(
                    'classes' => array(
                        'model' => 'Sylius\Component\Cart\Model\Cart'
                    )
                )
            ))
        );
    }
}
