<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Order;

class SyliusOrderEvents
{
    const PRE_CREATE = 'sylius.order.pre_create';
    const POST_CREATE = 'sylius.order.post_create';

    const PRE_UPDATE = 'sylius.order.pre_update';
    const POST_UPDATE = 'sylius.order.post_update';

    const PRE_DELETE = 'sylius.order.pre_delete';
    const POST_DELETE = 'sylius.order.post_delete';
}
