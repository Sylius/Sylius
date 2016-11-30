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
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Sylius\Component\Order\Context\CompositeCartContext;
use Sylius\Component\Order\Model\OrderInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class CompositeCartContextSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(CompositeCartContext::class);
    }

    function it_implements_cart_context_interface()
    {
        $this->shouldImplement(CartContextInterface::class);
    }

    function it_throws_cart_not_found_exception_if_there_are_no_nested_cart_contexts_defined()
    {
        $this->shouldThrow(CartNotFoundException::class)->during('getCart');
    }

    function it_throws_cart_not_found_exception_if_none_of_nested_cart_context_returned_a_cart(
        CartContextInterface $cartContext
    ) {
        $cartContext->getCart()->willThrow(CartNotFoundException::class);
        $this->addContext($cartContext);

        $this->shouldThrow(CartNotFoundException::class)->during('getCart');
    }

    function it_returns_cart_from_first_available_context(
        CartContextInterface $firstCartContext,
        CartContextInterface $secondCartContext,
        OrderInterface $cart
    ) {
        $firstCartContext->getCart()->willThrow(CartNotFoundException::class);
        $secondCartContext->getCart()->willReturn($cart);

        $this->addContext($firstCartContext);
        $this->addContext($secondCartContext);

        $this->getCart()->shouldReturn($cart);
    }

    function its_cart_contexts_can_have_priority(
        CartContextInterface $firstCartContext,
        CartContextInterface $secondCartContext,
        OrderInterface $cart
    ) {
        $firstCartContext->getCart()->shouldNotBeCalled();
        $secondCartContext->getCart()->willReturn($cart);

        $this->addContext($firstCartContext, -1);
        $this->addContext($secondCartContext, 0);

        $this->getCart()->shouldReturn($cart);
    }
}
