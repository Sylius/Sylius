<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Menu\Frontend;

use Knp\Menu\ItemInterface;
use Sylius\Bundle\UiBundle\Menu\MenuBuilder;

/**
 * Account panel menu builder.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class AccountMenuBuilder extends MenuBuilder
{
    /**
     * Creates user account menu.
     *
     * @return ItemInterface
     */
    public function createMenu()
    {
        $menu = $this->factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'nav'
            )
        ));

        $childOptions = array(
            'childrenAttributes' => array('class' => 'nav nav-list'),
            'labelAttributes'    => array('class' => 'nav-header')
        );

        $child = $menu->addChild('sylius.account.title', $childOptions);

        $child->addChild('account', array(
            'route' => 'sylius_account_homepage',
            'linkAttributes' => array('title' => 'sylius.frontend.menu.account.homepage'),
            'labelAttributes' => array('icon' => 'icon-home', 'iconOnly' => false)
        ))->setLabel('sylius.frontend.menu.account.homepage');

        $child->addChild('profile', array(
            'route' => 'fos_user_profile_edit',
            'linkAttributes' => array('title' => 'sylius.frontend.menu.account.profile'),
            'labelAttributes' => array('icon' => 'icon-info-sign', 'iconOnly' => false)
        ))->setLabel('sylius.frontend.menu.account.profile');

        $child->addChild('password', array(
            'route' => 'fos_user_change_password',
            'linkAttributes' => array('title' => 'sylius.frontend.menu.account.password'),
            'labelAttributes' => array('icon' => 'icon-lock', 'iconOnly' => false)
        ))->setLabel('sylius.frontend.menu.account.password');

        $child->addChild('orders', array(
            'route' => 'sylius_account_order_index',
            'linkAttributes' => array('title' => 'sylius.frontend.menu.account.orders'),
            'labelAttributes' => array('icon' => 'icon-briefcase', 'iconOnly' => false)
        ))->setLabel('sylius.frontend.menu.account.orders');

        $child->addChild('addresses', array(
            'route' => 'sylius_account_address_index',
            'linkAttributes' => array('title' => 'sylius.frontend.menu.account.addresses'),
            'labelAttributes' => array('icon' => 'icon-envelope', 'iconOnly' => false)
        ))->setLabel('sylius.frontend.menu.account.addresses');

        return $menu;
    }
}
