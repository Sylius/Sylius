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

namespace Sylius\Component\Core\Inventory\Operator;

use Sylius\Component\Core\Inventory\Exception\NotEnoughUnitsOnHandException;
use Sylius\Component\Core\Inventory\Exception\NotEnoughUnitsOnHoldException;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\OrderPaymentStates;

final class OrderInventoryOperator implements OrderInventoryOperatorInterface
{
    public function cancel(OrderInterface $order): void
    {
        if (in_array(
            $order->getPaymentState(),
            [OrderPaymentStates::STATE_PAID, OrderPaymentStates::STATE_REFUNDED],
            true,
        )) {
            $this->giveBack($order);

            return;
        }

        $this->release($order);
    }

    public function hold(OrderInterface $order): void
    {
        /** @var OrderItemInterface $orderItem */
        foreach ($order->getItems() as $orderItem) {
            $variant = $orderItem->getVariant();

            if (!$variant->isTracked()) {
                continue;
            }

            $variant->setOnHold($variant->getOnHold() + $orderItem->getQuantity());
        }
    }

    public function sell(OrderInterface $order): void
    {
        /** @var OrderItemInterface $orderItem */
        foreach ($order->getItems() as $orderItem) {
            $variant = $orderItem->getVariant();

            if (!$variant->isTracked()) {
                continue;
            }

            if (($variant->getOnHold() - $orderItem->getQuantity()) < 0) {
                throw new NotEnoughUnitsOnHoldException($variant->getName());
            }

            if (($variant->getOnHand() - $orderItem->getQuantity()) < 0) {
                throw new NotEnoughUnitsOnHandException($variant->getName());
            }

            $variant->setOnHold($variant->getOnHold() - $orderItem->getQuantity());
            $variant->setOnHand($variant->getOnHand() - $orderItem->getQuantity());
        }
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function release(OrderInterface $order): void
    {
        /** @var OrderItemInterface $orderItem */
        foreach ($order->getItems() as $orderItem) {
            $variant = $orderItem->getVariant();

            if (!$variant->isTracked()) {
                continue;
            }

            if (($variant->getOnHold() - $orderItem->getQuantity()) < 0) {
                throw new NotEnoughUnitsOnHoldException($variant->getName());
            }

            $variant->setOnHold($variant->getOnHold() - $orderItem->getQuantity());
        }
    }

    private function giveBack(OrderInterface $order): void
    {
        /** @var OrderItemInterface $orderItem */
        foreach ($order->getItems() as $orderItem) {
            $variant = $orderItem->getVariant();

            if (!$variant->isTracked()) {
                continue;
            }

            $variant->setOnHand($variant->getOnHand() + $orderItem->getQuantity());
        }
    }
}
