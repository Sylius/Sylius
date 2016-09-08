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
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class OnHandQuantityUpdater implements DecreasingQuantityUpdaterInterface
{
    /**
     * {@inheritdoc}
     */
    public function decrease(OrderInterface $order)
    {
        $orderItems = $order->getItems();
        foreach ($orderItems as $orderItem) {
            /** @var OrderItemInterface $orderItem */
            /** @var ProductVariantInterface $variant */
            $variant = $orderItem->getVariant();

            if ($variant->isTracked()) {
                Assert::greaterThanEq(($variant->getOnHand() - $orderItem->getQuantity()), 0, sprintf('Not enough units to decrease the inventory of a variant "%s".', $variant->getName()));

                $variant->setOnHand($variant->getOnHand() - $orderItem->getQuantity());
            }
        }
    }
}
