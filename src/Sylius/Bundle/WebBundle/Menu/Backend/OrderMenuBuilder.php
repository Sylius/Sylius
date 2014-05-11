<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Menu\Backend;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Sylius\Bundle\UiBundle\Menu\MenuBuilder;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Order menu builder.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderMenuBuilder extends ContainerAware
{
    const NAME = 'sylius.backend.order';

    /**
     * Builds order menu.
     *
     * @param FactoryInterface $factory
     *
     * @return ItemInterface
     */
    public function createMenu(FactoryInterface $factory, array $options)
    {
        $order = $options['order'];
        $id = $order->getId();

        $menu = $factory
            ->createItem('root')
            ->setChildrenAttribute('class', 'nav')
        ;

        $menu
            ->addChild('overview', array('uri' => '#overview'))
            ->setCurrent(true)
            ->setLinkAttribute('data-toggle', 'tab')
            ->setLabel('sylius.backend.order.overview')
        ;
        $menu
            ->addChild('shipments', array('uri' => '#shipments'))
            ->setLinkAttribute('data-toggle', 'tab')
            ->setLabel('sylius.backend.order.shipments')
        ;
        $menu
            ->addChild('payments', array('uri' => '#payments'))
            ->setLinkAttribute('data-toggle', 'tab')
            ->setLabel('sylius.backend.order.payments')
        ;
        $menu
            ->addChild('inventory', array('uri' => '#inventory'))
            ->setLinkAttribute('data-toggle', 'tab')
            ->setLabel('sylius.backend.order.inventory')
        ;

        return $menu;
    }
}
