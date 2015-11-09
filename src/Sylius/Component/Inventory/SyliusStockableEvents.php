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

class SyliusStockableEvents
{
    const PRE_INCREASE = 'sylius.stockable.pre_increase';
    const POST_INCREASE = 'sylius.stockable.post_increase';

    const PRE_DECREASE = 'sylius.stockable.pre_decrease';
    const POST_DECREASE = 'sylius.stockable.post_decrease';

    const PRE_HOLD = 'sylius.stockable.pre_hold';
    const POST_HOLD = 'sylius.stockable.post_hold';

    const PRE_RELEASE = 'sylius.stockable.pre_release';
    const POST_RELEASE = 'sylius.stockable.post_release';
}
