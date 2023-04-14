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

namespace Sylius\Bundle\ShopBundle\Calculator;

use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\OrderInterface;

trigger_deprecation(
    'sylius/sylius',
    '1.13',
    sprintf(
        'Class "%s" is deprecated and will be removed in Sylius 2.0. Items subtotal calculations is now available by using %s::getSubtotalItems method.',
        OrderItemsSubtotalCalculator::class,
        Order::class,
    ),
);

final class OrderItemsSubtotalCalculator implements OrderItemsSubtotalCalculatorInterface
{
    public function getSubtotal(OrderInterface $order): int
    {
        return $order->getItemsSubtotal();
    }
}
