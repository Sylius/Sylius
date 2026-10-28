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

final readonly class SalesMenuProvider implements MenuProviderInterface
{
    public function __invoke(ItemInterface $menu): void
    {
        $sales = $menu
            ->addChild('sales')
            ->setLabel('sylius.menu.admin.main.sales.header')
            ->setLabelAttribute('icon', 'tabler:shopping-bag')
            ->setExtra('always_open', true)
        ;

        $sales
            ->addChild('orders', ['route' => 'sylius_admin_order_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_order_update'],
                ['route' => 'sylius_admin_order_show'],
                ['route' => 'sylius_admin_order_history'],
            ]]])
            ->setLabel('sylius.menu.admin.main.sales.orders')
            ->setLabelAttribute('icon', 'tabler:shopping-cart')
        ;

        $sales
            ->addChild('payments', ['route' => 'sylius_admin_payment_index'])
            ->setLabel('sylius.ui.payments')
            ->setLabelAttribute('icon', 'tabler:credit-card-pay')
        ;

        $sales
            ->addChild('shipments', ['route' => 'sylius_admin_shipment_index', 'extras' => ['routes' => [
                ['route' => 'sylius_admin_shipment_show'],
            ]]])
            ->setLabel('sylius.ui.shipments')
            ->setLabelAttribute('icon', 'tabler:truck')
        ;

        $sales
            ->addChild('return_requests')
            ->setUri('https://sylius.com/plus/?utm_source=product&utm_medium=placeholder&utm_campaign=returns-placeholder')
            ->setLinkAttribute('target', '_blank')
            ->setLabel('sylius.menu.admin.main.sales.return_requests')
            ->setLabelAttribute('icon', 'tabler:truck-return')
            ->setExtra('plus_logo', true)
        ;

        $sales
            ->addChild('subscriptions')
            ->setUri('https://sylius.com/plus/?utm_source=product&utm_medium=placeholder&utm_campaign=subscriptions-placeholder')
            ->setLinkAttribute('target', '_blank')
            ->setLabel('sylius.menu.admin.main.sales.subscriptions')
            ->setLabelAttribute('icon', 'tabler:calendar-repeat')
            ->setExtra('plus_logo', true)
        ;
    }
}
