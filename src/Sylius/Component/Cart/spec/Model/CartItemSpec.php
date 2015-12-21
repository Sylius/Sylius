<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Cart\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Cart\Model\CartItemInterface;
use Sylius\Component\Order\Model\OrderItem;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CartItemSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Cart\Model\CartItem');
    }

    function it_implements_Sylius_cart_item_interface()
    {
        $this->shouldImplement(CartItemInterface::class);
    }

    function it_extends_Sylius_order_item()
    {
        $this->shouldHaveType(OrderItem::class);
    }
}
