<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Order\Modifier;

use Prophecy\Argument;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Modifier\OrderModifier;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class OrderModifierSpec extends ObjectBehavior
{
    function let(
        OrderProcessorInterface $orderProcessor,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier
    ) {
        $this->beConstructedWith($orderProcessor, $orderItemQuantityModifier);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OrderModifier::class);
    }

    function it_implements_an_order_modifier_interface()
    {
        $this->shouldImplement(OrderModifierInterface::class);
    }

    function it_adds_new_item_to_order_if_it_is_empty(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderProcessorInterface $orderProcessor
    ) {
        $order->getItems()->willReturn([]);

        $order->addItem($orderItem)->shouldBeCalled();
        $orderProcessor->process($order)->shouldBeCalled();

        $this->addToOrder($order, $orderItem);
    }

    function it_adds_new_item_to_an_order_if_different_order_item_is_in_an_order(
        OrderInterface $order,
        OrderItemInterface $existingItem,
        OrderItemInterface $newItem,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        OrderProcessorInterface $orderProcessor
    ) {
        $order->getItems()->willReturn([$existingItem]);

        $newItem->equals($existingItem)->willReturn(false);

        $orderItemQuantityModifier->modify(Argument::type(OrderInterface::class), Argument::any())->shouldNotBeCalled();

        $order->addItem($newItem)->shouldBeCalled();
        $orderProcessor->process($order)->shouldBeCalled();

        $this->addToOrder($order, $newItem);
    }

    function it_changes_quantity_of_an_item_if_same_order_item_already_exists(
        OrderInterface $order,
        OrderItemInterface $existingItem,
        OrderItemInterface $newItem,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        OrderProcessorInterface $orderProcessor
    ) {
        $order->getItems()->willReturn([$existingItem]);

        $newItem->equals($existingItem)->willReturn(true);
        $existingItem->getQuantity()->willReturn(2);

        $newItem->getQuantity()->willReturn(3);

        $order->addItem($existingItem)->shouldNotBeCalled();
        $orderItemQuantityModifier->modify($existingItem, 5)->shouldBeCalled();
        $orderProcessor->process($order)->shouldBeCalled();

        $this->addToOrder($order, $newItem);
    }

    function it_removes_an_order_item_from_an_order(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderProcessorInterface $orderProcessor
    ) {
        $order->removeItem($orderItem)->shouldBeCalled();
        $orderProcessor->process($order)->shouldBeCalled();

        $this->removeFromOrder($order, $orderItem);
    }
}
