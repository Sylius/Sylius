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

namespace Sylius\Component\Order\Modifier;

use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

final class OrderModifier implements OrderModifierInterface
{
    /**
     * @var OrderProcessorInterface
     */
    private $orderProcessor;

    /**
     * @var OrderItemQuantityModifierInterface
     */
    private $orderItemQuantityModifier;

    /**
     * @param OrderProcessorInterface $orderProcessor
     * @param OrderItemQuantityModifierInterface $orderItemQuantityModifier
     */
    public function __construct(
        OrderProcessorInterface $orderProcessor,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier
    ) {
        $this->orderProcessor = $orderProcessor;
        $this->orderItemQuantityModifier = $orderItemQuantityModifier;
    }

    /**
     * {@inheritdoc}
     */
    public function addToOrder(OrderInterface $order, OrderItemInterface $item): void
    {
        $this->resolveOrderItem($order, $item);

        $this->orderProcessor->process($order);
    }

    /**
     * {@inheritdoc}
     */
    public function removeFromOrder(OrderInterface $order, OrderItemInterface $item): void
    {
        $order->removeItem($item);
        $this->orderProcessor->process($order);
    }

    /**
     * @param OrderInterface $order
     * @param OrderItemInterface $item
     */
    private function resolveOrderItem(OrderInterface $order, OrderItemInterface $item): void
    {
        foreach ($order->getItems() as $existingItem) {
            if ($item->equals($existingItem)) {
                $this->orderItemQuantityModifier->modify(
                    $existingItem,
                    $existingItem->getQuantity() + $item->getQuantity()
                );

                return;
            }
        }

        $order->addItem($item);
    }
}
