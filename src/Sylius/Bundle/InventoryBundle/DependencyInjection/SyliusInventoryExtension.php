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

use Sylius\Bundle\ResourceBundle\DependencyInjection\AbstractResourceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Inventory extension.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class SyliusInventoryExtension extends AbstractResourceExtension
{
    protected $configFiles = array(
        'services',
        'templating',
        'twig',
    );

    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        list($config) = $this->configure(
            $config,
            new Configuration(),
            $container,
            self::CONFIGURE_LOADER | self::CONFIGURE_DATABASE | self::CONFIGURE_PARAMETERS
        );

        $container->setParameter('sylius.backorders', $config['backorders']);

        $container->setAlias('sylius.availability_checker', $config['checker']);
        $container->setAlias('sylius.inventory_operator', $config['operator']);

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
