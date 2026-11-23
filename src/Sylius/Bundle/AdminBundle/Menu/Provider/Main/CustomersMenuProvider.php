<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Menu\Provider\Main;

use Knp\Menu\ItemInterface;
use Sylius\Bundle\AdminBundle\Menu\Provider\MenuProviderInterface;

final readonly class CustomersMenuProvider implements MenuProviderInterface
{
    public function __invoke(ItemInterface $menu): void
    {
        $customers = $menu
            ->addChild('customers')
            ->setLabel('sylius.menu.admin.main.customers.header')
            ->setLabelAttribute('icon', 'tabler:users')
            ->setExtra('always_open', true)
        ;

        $customers
            ->addChild('customers', ['route' => 'sylius_admin_customer_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_customer_create'],
                ['route' => 'sylius_admin_customer_update'],
                ['route' => 'sylius_admin_customer_show'],
            ]]])
            ->setLabel('sylius.menu.admin.main.customers.customers')
            ->setLabelAttribute('icon', 'tabler:users')
        ;

        $customers
            ->addChild('groups', ['route' => 'sylius_admin_customer_group_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_customer_group_create'],
                ['route' => 'sylius_admin_customer_group_update'],
            ]]])
            ->setLabel('sylius.menu.admin.main.customers.groups')
            ->setLabelAttribute('icon', 'tabler:archive')
        ;
    }
}
