<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Modifier;

use Prophecy\Argument;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Cart\Model\CartItemInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Modifier\CartModifier;
use Sylius\Component\Core\Modifier\CartModifierInterface;
use Sylius\Component\Core\OrderProcessing\OrderProcessorInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;

/**
 * @mixin CartModifier
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class CartModifierSpec extends ObjectBehavior
{
    function let(OrderProcessorInterface $orderProcessor, OrderItemQuantityModifierInterface $orderItemQuantityModifier)
    {
        $this->beConstructedWith($orderProcessor, $orderItemQuantityModifier);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CartModifier::class);
    }

    function it_implements_cart_modifier_interface()
    {
        $this->shouldImplement(CartModifierInterface::class);
    }

    function it_adds_new_item_to_cart_if_it_is_empty(
        OrderProcessorInterface $orderProcessor,
        OrderInterface $cart,
        CartItemInterface $cartItem
    ) {
        $cart->getItems()->willReturn([]);

        $cart->addItem($cartItem)->shouldBeCalled();
        $orderProcessor->process($cart)->shouldBeCalled();

        $this->addToCart($cart, $cartItem);
    }

    function it_adds_new_item_to_cart_if_different_cart_item_is_in_a_cart(
        OrderProcessorInterface $orderProcessor,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        OrderInterface $cart,
        CartItemInterface $newItem,
        CartItemInterface $existingItem
    ) {
        $cart->getItems()->willReturn([$existingItem]);

        $newItem->equals($existingItem)->willReturn(false);

        $orderItemQuantityModifier->modify(Argument::type(OrderInterface::class), Argument::any())->shouldNotBeCalled();

        $cart->addItem($newItem)->shouldBeCalled();
        $orderProcessor->process($cart)->shouldBeCalled();

        $this->addToCart($cart, $newItem);
    }

    function it_changes_quntity_of_item_if_same_cart_item_already_exists(
        OrderProcessorInterface $orderProcessor,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        OrderInterface $cart,
        CartItemInterface $newItem,
        CartItemInterface $existingItem
    ) {
        $cart->getItems()->willReturn([$existingItem]);

        $newItem->equals($existingItem)->willReturn(true);
        $existingItem->getQuantity()->willReturn(2);

        $newItem->getQuantity()->willReturn(3);

        $cart->addItem($existingItem)->shouldNotBeCalled();
        $orderItemQuantityModifier->modify($existingItem, 5)->shouldBeCalled();
        $orderProcessor->process($cart)->shouldBeCalled();

        $this->addToCart($cart, $newItem);
    }
}
