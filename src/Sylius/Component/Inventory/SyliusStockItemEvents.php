<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory;

class SyliusStockItemEvents
{
    const PRE_INCREASE  = 'sylius.stock_item.pre_increase';
    const POST_INCREASE = 'sylius.stock_item.post_increase';

    const PRE_DECREASE  = 'sylius.stock_item.pre_decrease';
    const POST_DECREASE = 'sylius.stock_item.post_decrease';

    const PRE_HOLD      = 'sylius.stock_item.pre_hold';
    const POST_HOLD     = 'sylius.stock_item.post_hold';

    const PRE_RELEASE   = 'sylius.stock_item.pre_release';
    const POST_RELEASE  = 'sylius.stock_item.post_release';
}
