<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Cart\Model;

use Sylius\Component\Order\Model\OrderItemInterface;

/**
 * Interface for cart item model.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface CartItemInterface extends OrderItemInterface
{
}
