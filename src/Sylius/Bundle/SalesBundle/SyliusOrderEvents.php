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

final class SyliusOrderEvents
{
	const ORDER_PRE_CREATE  = 'sylius.order.pre_create';
	const ORDER_POST_CREATE = 'sylius.order.post_create';
}
