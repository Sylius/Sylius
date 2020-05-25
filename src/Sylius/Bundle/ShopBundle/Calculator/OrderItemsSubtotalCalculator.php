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

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;

final class OrderItemsSubtotalCalculator implements OrderItemsSubtotalCalculatorInterface
{
    public function getSubtotal(OrderInterface $order): int
    {
        return array_reduce(
            $order->getItems()->toArray(),
            static function (int $subtotal, OrderItemInterface $item): int {
                return $subtotal + $item->getSubtotal();
            },
            0
        );
    }
}
