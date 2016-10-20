<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShopBundle\Menu;

use Knp\Menu\ItemInterface;
use Sylius\Bundle\UiBundle\Menu\AbstractMenuBuilder;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class AccountMenuBuilder extends AbstractMenuBuilder
{
    const EVENT_NAME = 'sylius.menu.shop.account';

    /**
     * @return ItemInterface
     */
    public function createMenu()
    {
        $menu = $this->factory->createItem('root');
        $menu->setLabel('sylius.menu.shop.account.header');

        $menu
            ->addChild('dashboard', ['route' => 'sylius_shop_account_dashboard'])
            ->setLabel('sylius.menu.shop.account.dashboard')
            ->setLabelAttribute('icon', 'home')
        ;
        $menu
            ->addChild('personal_information', ['route' => 'sylius_shop_account_profile_update'])
            ->setLabel('sylius.menu.shop.account.personal_information')
            ->setLabelAttribute('icon', 'user')
        ;
        $menu
            ->addChild('change_password', ['route' => 'sylius_shop_account_change_password'])
            ->setLabel('sylius.menu.shop.account.change_password')
            ->setLabelAttribute('icon', 'lock')
        ;
        $menu
            ->addChild('address_book', ['route' => 'sylius_shop_account_address_book_index'])
            ->setLabel('sylius.menu.shop.account.address_book')
            ->setLabelAttribute('icon', 'book')
        ;
        $menu
            ->addChild('order_history', ['route' => 'sylius_shop_account_order_index'])
            ->setLabel('sylius.menu.shop.account.order_history')
            ->setLabelAttribute('icon', 'cart')
        ;

        $this->eventDispatcher->dispatch(self::EVENT_NAME, new MenuBuilderEvent($this->factory, $menu));

        return $menu;
    }
}
