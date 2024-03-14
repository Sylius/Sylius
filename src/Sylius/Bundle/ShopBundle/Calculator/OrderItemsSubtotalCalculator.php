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

namespace Sylius\Bundle\ShopBundle\Calculator;

use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\OrderInterface;

trigger_deprecation(
    'sylius/sylius',
    '1.13',
    'The "%s" class is deprecated and will be removed in Sylius 2.0. Items subtotal calculations is now available by using %s::getSubtotalItems method.',
    OrderItemsSubtotalCalculator::class,
    Order::class,
);

/**
 * @deprecated since Sylius 1.13 and will be removed in Sylius 2.0. Items subtotal calculations is now available by using {@see Order::getSubtotalItems} method.
 */
final class OrderItemsSubtotalCalculator implements OrderItemsSubtotalCalculatorInterface
{
    public function getSubtotal(OrderInterface $order): int
    {
        return $order->getItemsSubtotal();
    }
}
