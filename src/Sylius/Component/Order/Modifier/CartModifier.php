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
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

/**
 * @author Łukasz Chrusciel <lukasz.chrusciel@lakion.com>
 */
final class CartModifier implements CartModifierInterface
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
     * @param OrderInterface $cart
     * @param OrderItemInterface $item
     */
    public function addToCart(OrderInterface $cart, OrderItemInterface $item)
    {
        $this->resolveCartItem($cart, $item);

        $this->orderProcessor->process($cart);
    }

    /**
     * @param OrderInterface $cart
     * @param OrderItemInterface $item
     */
    public function removeFromCart(OrderInterface $cart, OrderItemInterface $item)
    {
        $cart->removeItem($item);
        $this->orderProcessor->process($cart);
    }

    /**
     * @param OrderInterface $cart
     * @param OrderItemInterface $item
     */
    private function resolveCartItem(OrderInterface $cart, OrderItemInterface $item)
    {
        foreach ($cart->getItems() as $existingItem) {
            if ($item->equals($existingItem)) {
                $this->orderItemQuantityModifier->modify($existingItem, $existingItem->getQuantity() + $item->getQuantity());

                return;
            }
        }

        $cart->addItem($item);
    }
}
