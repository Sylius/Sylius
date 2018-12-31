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

namespace spec\Sylius\Bundle\OrderBundle\Controller;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;

final class AddToCartCommandSpec extends ObjectBehavior
{
    function let(OrderInterface $order, OrderItemInterface $orderItem): void
    {
        $this->beConstructedThrough('createWithCartAndCartItem', [$order, $orderItem]);
    }

    function it_is_add_cart_item_command(): void
    {
        $this->shouldImplement(AddToCartCommandInterface::class);
    }

    function it_has_order(OrderInterface $order): void
    {
        $this->getCart()->shouldReturn($order);
    }

    function it_has_order_item(OrderItemInterface $orderItem): void
    {
        $this->getCartItem()->shouldReturn($orderItem);
    }
}
