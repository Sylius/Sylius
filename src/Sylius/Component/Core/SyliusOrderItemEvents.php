<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core;

class SyliusOrderItemEvents
{
    const PRE_CREATE = 'sylius.order_item.pre_create';
    const PRE_UPDATE = 'sylius.order_item.pre_update';
}
