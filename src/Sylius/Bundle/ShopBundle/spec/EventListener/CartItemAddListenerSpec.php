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

namespace spec\Sylius\Bundle\ShopBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class CartItemAddListenerSpec extends ObjectBehavior
{
    function let(OrderModifierInterface $orderModifier): void
    {
        $this->beConstructedWith($orderModifier);
    }

    function it_adds_cart_item_to_order(
        OrderModifierInterface $orderModifier,
        GenericEvent $event,
        AddToCartCommandInterface $addToCartCommand,
        OrderItemInterface $orderItem,
        OrderInterface $order,
    ): void {
        $addToCartCommand->getCart()->willReturn($order);
        $addToCartCommand->getCartItem()->willReturn($orderItem);
        $event->getSubject()->willReturn($addToCartCommand);

        $this->addToOrder($event);

        $orderModifier->addToOrder($order, $orderItem)->shouldHaveBeenCalled();
    }

    function it_throws_exception_if_event_subject_is_not_add_to_cart_command(
        GenericEvent $event,
    ): void {
        $event->getSubject()->willReturn(new \stdClass());

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('addToOrder', [$event])
        ;
    }
}
