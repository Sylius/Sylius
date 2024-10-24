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

namespace Sylius\Component\Core\Inventory\Checker;

use Sylius\Component\Core\Model\OrderItemInterface;

final class OrderItemAvailabilityChecker implements OrderItemAvailabilityCheckerInterface
{
    public function isReservedStockSufficient(OrderItemInterface $orderItem): bool
    {
        $variant = $orderItem->getVariant();
        if (!$variant->isTracked()) {
            return true;
        }

        $quantity = $orderItem->getQuantity();

        return
            $variant->getOnHold() - $quantity >= 0 &&
            $variant->getOnHand() - $quantity >= 0
        ;
    }
}
