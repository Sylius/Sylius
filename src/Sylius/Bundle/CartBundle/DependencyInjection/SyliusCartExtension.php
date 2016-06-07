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
        $config = $this->processConfiguration($this->getConfiguration($config, $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load(sprintf('driver/%s.xml', $config['driver']));

        $this->registerResources('sylius', $config['driver'], $config['resources'], $container);

        $configFiles = [
            'services.xml',
            'providers.xml',
            'templating.xml',
            'twig.xml',
        ];

        foreach ($configFiles as $configFile) {
            $loader->load($configFile);
        }

        $container->setAlias('sylius.cart_resolver', $config['resolver']);

        $definition = $container->getDefinition('sylius.form.type.cart_item');
        $definition->addArgument(new Reference('sylius.form.data_mapper.order_item_quantity'));
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        if (!$container->hasExtension('sylius_order')) {
            throw new \RuntimeException('Please install and configure SyliusOrderBundle in order to use SyliusCartBundle.');
        }

        $container->prependExtensionConfig('sylius_order', [
            'resources' => [
                'order' => [
                    'classes' => [
                        'model' => Cart::class,
                    ],
                ],
                'order_item' => [
                    'classes' => [
                        'model' => CartItem::class,
                    ],
                ],
            ], ]
        );
    }
}
