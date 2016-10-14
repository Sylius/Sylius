<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Order\Modifier;

use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

/**
 * @author Łukasz Chrusciel <lukasz.chrusciel@lakion.com>
 */
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
     * @param OrderInterface $order
     * @param OrderItemInterface $item
     */
    public function addToOrder(OrderInterface $order, OrderItemInterface $item)
    {
        $this->resolveOrderItem($order, $item);

        $this->orderProcessor->process($order);
    }

    /**
     * @param OrderInterface $order
     * @param OrderItemInterface $item
     */
    public function removeFromOrder(OrderInterface $order, OrderItemInterface $item)
    {
        $order->removeItem($item);
        $this->orderProcessor->process($order);
    }

    /**
     * @param OrderInterface $order
     * @param OrderItemInterface $item
     */
    private function resolveOrderItem(OrderInterface $order, OrderItemInterface $item)
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
