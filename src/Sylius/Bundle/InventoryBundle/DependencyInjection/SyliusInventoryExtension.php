<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InventoryBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Inventory extension.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class SyliusInventoryExtension extends AbstractResourceExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $this->registerResources('sylius', $config['driver'], $config['resources'], $container);

        $configFiles = array(
            'twig.xml',
            'templating.xml',
            'services.xml',
        );

        foreach ($configFiles as $configFile) {
            $loader->load($configFile);
        }

        $container->setParameter('sylius.backorders', $config['backorders']);

        $container->setAlias('sylius.availability_checker', $config['checker']);
        $container->setAlias('sylius.inventory_operator', $config['operator']);

        $container
            ->getDefinition('sylius.factory.stock_item')
            ->addArgument(new Reference('sylius.repository.stock_item'))
            ->addArgument(new Reference('sylius.manager.stock_item'))
            ->addArgument(new Reference('sylius.repository.stock_location'))
            ->addArgument(new Reference('sylius.repository.stockable'));

        $container
            ->getDefinition('sylius.factory.stock_movement')
            ->addArgument(new Reference('sylius.repository.stock_movement'));

        $container
            ->getDefinition('sylius.form.type.stock_movement')
            ->addMethodCall('setStockableRepository', [new Reference('sylius.repository.stockable')]);

        if (isset($config['events'])) {
            $listenerDefinition = $container->getDefinition('sylius.listener.inventory');

            foreach ($config['events'] as $event) {
                $listenerDefinition->addTag(
                    'kernel.event_listener',
                    array('event' => $event, 'method' => 'onInventoryChange')
                );
            }
        }
    }
}
