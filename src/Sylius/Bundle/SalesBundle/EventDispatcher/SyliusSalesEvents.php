<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\EventDispatcher;

/**
 * Sales events.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
final class SyliusSalesEvents
{
    const ORDER_CLOSE          = 'sylius_sales.event.order.close';
    const ORDER_CONFIRM        = 'sylius_sales.event.order.confirm';
    const ORDER_CREATE         = 'sylius_sales.event.order.create';
    const ORDER_DELETE         = 'sylius_sales.event.order.delete';
    const ORDER_OPEN           = 'sylius_sales.event.order.close';
    const ORDER_PLACE          = 'sylius_sales.event.order.place';
    const ORDER_PREPARE        = 'sylius_sales.event.order.prepare';
    const ORDER_PROCESS        = 'sylius_sales.event.order.process';
    const ORDER_CHANGE_STATUS  = 'sylius_sales.event.order.change_status';
    const ORDER_UPDATE         = 'sylius_sales.event.order.update';

    const STATUS_CREATE        = 'sylius_sales_event.status.create';
    const STATUS_DELETE        = 'sylius_sales.event.status.delete';
    const STATUS_UPDATE        = 'sylius_sales.event.status.update';
}
