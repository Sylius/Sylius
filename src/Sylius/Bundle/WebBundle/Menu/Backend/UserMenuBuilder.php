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
use Sylius\Component\Core\Model\UserInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * User menu builder.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class UserMenuBuilder extends ContainerAware
{
    const NAME = 'sylius.backend.user';

    /**
     * Builds user menu.
     *
     * @param FactoryInterface $factory
     *
     * @return ItemInterface
     */
    public function createMenu(FactoryInterface $factory, array $options)
    {
        $user = $options['user'];
        $id = $user->getId();

        $menu = $factory
            ->createItem('root')
            ->setChildrenAttribute('class', 'nav')
        ;

        $menu
            ->addChild('overview', array('uri' => '#overview'))
            ->setLinkAttribute('data-toggle', 'tab')
            ->setLabel('sylius.backend.user.overview')
        ;
        $menu
            ->addChild('details', array('uri' => '#details'))
            ->setLinkAttribute('data-toggle', 'tab')
            ->setLabel('sylius.backend.user.details')
        ;
        $menu
            ->addChild('addresses', array('uri' => '#addresses'))
            ->setLinkAttribute('data-toggle', 'tab')
            ->setLabel('sylius.backend.user.addresses')
        ;
        $menu
            ->addChild('orders', array('uri' => '#orders'))
            ->setLinkAttribute('data-toggle', 'tab')
            ->setLabel('sylius.backend.user.orders')
        ;

        return $menu;
    }
}
