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

namespace Sylius\Component\Order\Modifier;

use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

final class OrderModifier implements OrderModifierInterface
{
    public function __construct(
        private OrderProcessorInterface $orderProcessor,
        private OrderItemQuantityModifierInterface $orderItemQuantityModifier,
    ) {
    }

    public function addToOrder(OrderInterface $cart, OrderItemInterface $cartItem): void
    {
        $this->resolveOrderItem($cart, $cartItem);

        $this->orderProcessor->process($cart);
    }

    public function removeFromOrder(OrderInterface $cart, OrderItemInterface $item): void
    {
        $cart->removeItem($item);

        $this->orderProcessor->process($cart);
    }

    private function resolveOrderItem(OrderInterface $cart, OrderItemInterface $item): void
    {
        foreach ($cart->getItems() as $existingItem) {
            if ($item->equals($existingItem)) {
                $this->orderItemQuantityModifier->modify(
                    $existingItem,
                    $existingItem->getQuantity() + $item->getQuantity(),
                );

                return;
            }
        }

        $cart->addItem($item);
    }
}
