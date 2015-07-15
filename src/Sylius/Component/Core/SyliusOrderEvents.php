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

use Sylius\Component\Order\SyliusOrderEvents as BaseSyliusOrderEvents;

class SyliusOrderEvents extends BaseSyliusOrderEvents
{
    const PRE_RELEASE = 'sylius.order.pre_release';
    const POST_RELEASE = 'sylius.order.post_release';
}
