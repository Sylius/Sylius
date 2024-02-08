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

namespace Sylius\Bundle\OrderBundle;

interface SyliusExpiredCartsEvents
{
    public const PRE_REMOVE = 'sylius.carts.pre_remove';

    public const POST_REMOVE = 'sylius.carts.post_remove';
}
