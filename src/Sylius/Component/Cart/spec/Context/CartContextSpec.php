<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Cart\Context;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Cart\Context\CartContext;
use Sylius\Component\Cart\Context\CartContextInterface;
use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @mixin CartContext
 *
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
        $this->shouldHaveType('Sylius\Component\Cart\Context\CartContext');
    }
    
    function it_implements_cart_context_interface()
    {
        $this->shouldImplement(CartContextInterface::class);
    }

    function it_always_returns_a_new_cart(FactoryInterface $cartFactory, CartInterface $cart)
    {
        $cartFactory->createNew()->willReturn($cart);

        $this->getCart()->shouldReturn($cart);
    }
}
