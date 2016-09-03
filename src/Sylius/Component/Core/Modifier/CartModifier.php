<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Modifier;

use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Cart\Model\CartItemInterface;
use Sylius\Component\Core\OrderProcessing\OrderProcessorInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;

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
     * @param CartInterface $cart
     * @param CartItemInterface $item
     */
    public function addToCart(CartInterface $cart, CartItemInterface $item)
    {
        $this->resolveCartItem($cart, $item);

        $this->orderProcessor->process($cart);
    }

    /**
     * @param CartInterface $cart
     * @param CartItemInterface $item
     */
    public function removeFromCart(CartInterface $cart, CartItemInterface $item)
    {
        $cart->removeItem($item);
        $this->orderProcessor->process($cart);
    }

    /**
     * @param CartInterface $cart
     * @param CartItemInterface $item
     */
    private function resolveCartItem(CartInterface $cart, CartItemInterface $item)
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
