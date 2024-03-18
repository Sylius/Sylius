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

use Sylius\Component\Core\Model\OrderInterface;

trigger_deprecation(
    'sylius/shop-bundle',
    '1.13',
    'The "%s" interface is deprecated and will be removed in Sylius 2.0, use method "getItemsSubtotal" from "%s" instead.',
    OrderItemsSubtotalCalculatorInterface::class,
    OrderInterface::class,
);

/**
 * @deprecated since Sylius 1.13 and will be removed in Sylius 2.0. Items subtotal calculations is now available by using {@see Order::getSubtotalItems} method.
 */
interface OrderItemsSubtotalCalculatorInterface
{
    public function getSubtotal(OrderInterface $order): int;
}
