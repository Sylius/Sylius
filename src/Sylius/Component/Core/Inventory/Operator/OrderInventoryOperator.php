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

namespace Sylius\Component\Core\Inventory\Operator;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Webmozart\Assert\Assert;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class OrderInventoryOperator implements OrderInventoryOperatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function cancel(OrderInterface $order)
    {
        if (in_array(
            $order->getPaymentState(),
            [OrderPaymentStates::STATE_PAID, OrderPaymentStates::STATE_REFUNDED],
            true
        )) {
            $this->giveBack($order);

            return;
        }

        $this->release($order);
    }

    /**
     * {@inheritdoc}
     */
    public function hold(OrderInterface $order)
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

    /**
     * {@inheritdoc}
     */
    public function sell(OrderInterface $order)
    {
        /** @var OrderItemInterface $orderItem */
        foreach ($order->getItems() as $orderItem) {
            $variant = $orderItem->getVariant();

            if (!$variant->isTracked()) {
                continue;
            }

            Assert::greaterThanEq(
                ($variant->getOnHold() - $orderItem->getQuantity()),
                0,
                sprintf(
                    'Not enough units to decrease on hold quantity from the inventory of a variant "%s".',
                    $variant->getName()
                )
            );

            Assert::greaterThanEq(
                ($variant->getOnHand() - $orderItem->getQuantity()),
                0,
                sprintf(
                    'Not enough units to decrease on hand quantity from the inventory of a variant "%s".',
                    $variant->getName()
                )
            );

            $variant->setOnHold($variant->getOnHold() - $orderItem->getQuantity());
            $variant->setOnHand($variant->getOnHand() - $orderItem->getQuantity());
        }
    }

    /**
     * @param OrderInterface $order
     *
     * @throws \InvalidArgumentException
     */
    private function release(OrderInterface $order)
    {
        /** @var OrderItemInterface $orderItem */
        foreach ($order->getItems() as $orderItem) {
            $variant = $orderItem->getVariant();

            if (!$variant->isTracked()) {
                continue;
            }

            Assert::greaterThanEq(
                ($variant->getOnHold() - $orderItem->getQuantity()),
                0,
                sprintf(
                    'Not enough units to decrease on hold quantity from the inventory of a variant "%s".',
                    $variant->getName()
                )
            );

            $variant->setOnHold($variant->getOnHold() - $orderItem->getQuantity());
        }
    }

    /**
     * @param OrderInterface $order
     */
    private function giveBack(OrderInterface $order)
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
