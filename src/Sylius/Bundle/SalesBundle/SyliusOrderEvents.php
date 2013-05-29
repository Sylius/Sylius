<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle;

final class SyliusOrderEvents
{
    const ORDER_CREATE_INITIALIZE = 'sylius.order_create.initialize';
    const ORDER_CREATE_COMPLETED  = 'sylius.order_create.completed';

    const ORDER_UPDATE_INITIALIZE = 'sylius.order_update.initialize';
    const ORDER_UPDATE_COMPLETED  = 'sylius.order_update.completed';
}
