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

use Sylius\Component\Core\Model\OrderItemInterface;
use Webmozart\Assert\Assert;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class OnHandQuantityUpdater implements OnHandQuantityUpdaterInterface
{
    /**
     * {@inheritdoc}
     */
    public function decrease($orderItems)
    {
        foreach ($orderItems as $orderItem) {
            Assert::greaterThanEq($orderItem->getQuantity(), 0, 'Quantity of order items cannot be negative number.');

            /** @var OrderItemInterface $orderItem*/
            $variant = $orderItem->getVariant();

            Assert::greaterThanEq(($variant->getOnHand() - $orderItem->getQuantity()), 0, 'Quantity of variant items on hand cannot be negative number.');
            
            $variant->setOnHand($variant->getOnHand() - $orderItem->getQuantity());
        }
    }
}
