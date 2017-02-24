<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle;

final class SyliusExpiredCartsEvents
{
    const PRE_REMOVE = 'sylius.carts.pre_remove';
    const POST_REMOVE = 'sylius.carts.post_remove';

    private function __construct()
    {
    }
}
