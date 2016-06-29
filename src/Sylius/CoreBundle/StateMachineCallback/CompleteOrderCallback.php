<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\CoreBundle\StateMachineCallback;

use Sylius\OrderBundle\StateMachineCallback\CompleteOrderCallback as BaseCompleteOrderCallback;
use Sylius\Core\Model\OrderInterface;
use Sylius\Core\Model\OrderItemInterface;

/**
 * Set update sold value for items in order.
 */
class CompleteOrderCallback extends BaseCompleteOrderCallback
{
    /**
     * @param OrderInterface $order
     */
    public function increaseSoldVariants(OrderInterface $order)
    {
        /** @var $item OrderItemInterface */
        foreach ($order->getItems() as $item) {
            $variant = $item->getVariant();
            $variant->setSold($variant->getSold() + $item->getQuantity());
        }
    }

    /**
     * @param OrderInterface $order
     */
    public function decreaseSoldVariants(OrderInterface $order)
    {
        /** @var $item OrderItemInterface */
        foreach ($order->getItems() as $item) {
            $variant = $item->getVariant();
            $variant->setSold($variant->getSold() - $item->getQuantity());
        }
    }
}
