<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Inventory\Updater;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class OnHoldQuantityUpdater implements IncreasingQuantityUpdaterInterface, DecreasingQuantityUpdaterInterface
{
    /**
     * {@inheritdoc}
     */
    public function increase(OrderInterface $order)
    {
        foreach ($order->getItems() as $orderItem) {
            /** @var OrderItemInterface $orderItem */
            /** @var ProductVariantInterface $variant */
            $variant = $orderItem->getVariant();

            if ($variant->isTracked()) {
                $variant->setOnHold($variant->getOnHold() + $orderItem->getQuantity());
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function decrease(OrderInterface $order)
    {
        foreach ($order->getItems() as $orderItem) {
            /** @var OrderItemInterface $orderItem */
            /** @var ProductVariantInterface $variant */
            $variant = $orderItem->getVariant();

            if ($variant->isTracked()) {
                Assert::greaterThanEq(
                    ($variant->getOnHold() - $orderItem->getQuantity()),
                    0,
                    sprintf('Not enough units to decrease the inventory of a variant "%s".', $variant->getName())
                );

                $variant->setOnHold($variant->getOnHold() - $orderItem->getQuantity());
            }
        }
    }
}
