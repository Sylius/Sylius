<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Order\Context;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Order\Context\CartContext;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class CartContextSpec extends ObjectBehavior
{
    function let(FactoryInterface $cartFactory)
    {
        $this->beConstructedWith($cartFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CartContext::class);
    }

    function it_implements_cart_context_interface()
    {
        $this->shouldImplement(CartContextInterface::class);
    }

    function it_always_returns_a_new_cart(FactoryInterface $cartFactory, OrderInterface $cart)
    {
        $cartFactory->createNew()->willReturn($cart);

        $this->getCart()->shouldReturn($cart);
    }
}
