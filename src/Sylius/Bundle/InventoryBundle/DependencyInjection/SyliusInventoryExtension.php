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

use Sylius\Bundle\ResourceBundle\DependencyInjection\BaseExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Inventory dependency injection extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
class SyliusInventoryExtension extends BaseExtension
{
    protected $configFiles = array(
        'services',
        'twig',
    );

    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $this->configDir = __DIR__.'/../Resources/config';

        list($config) = $this->configure($config, new Configuration(), $container, self::CONFIGURE_LOADER | self::CONFIGURE_DATABASE);

        $container->setParameter('sylius.backorders', $config['backorders']);

        $container->setAlias('sylius.availability_checker', $config['checker']);
        $container->setAlias('sylius.inventory_operator', $config['operator']);

        $classes = $config['classes'];

        $container->setParameter('sylius.controller.inventory_unit.class', $classes['inventory_unit']['controller']);
        $container->setParameter('sylius.model.inventory_unit.class', $classes['inventory_unit']['model']);

        if (array_key_exists('repository', $classes['inventory_unit'])) {
            $container->setParameter('sylius.repository.inventory_unit.class', $classes['inventory_unit']['repository']);
        }

        $container->setParameter('sylius.model.stockable.class', $classes['stockable']['model']);

        if (isset($config['events'])) {
            $listenerDefinition = $container->getDefinition('sylius.inventory_listener');
            foreach ($config['events'] as $event) {
                $listenerDefinition->addTag('kernel.event_listener', array('event' => $event, 'method' => 'onInventoryChange'));
            }
        }

        if ($container->hasParameter('sylius.config.classes')) {
            $classes = array_merge($classes, $container->getParameter('sylius.config.classes'));
        }

        $container->setParameter('sylius.config.classes', $classes);
    }
}
