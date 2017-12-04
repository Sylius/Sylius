<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Order\Modifier;

use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;

interface OrderModifierInterface
{
    /**
     * @param OrderInterface $cart
     * @param OrderItemInterface $cartItem
     */
    public function addToOrder(OrderInterface $cart, OrderItemInterface $cartItem): void;

    /**
     * @param OrderInterface $cart
     * @param OrderItemInterface $item
     */
    public function removeFromOrder(OrderInterface $cart, OrderItemInterface $item): void;
}
