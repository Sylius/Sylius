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

namespace spec\Sylius\Component\Order\Context;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class CartContextSpec extends ObjectBehavior
{
    function let(FactoryInterface $cartFactory): void
    {
        $this->beConstructedWith($cartFactory);
    }

    function it_implements_cart_context_interface(): void
    {
        $this->shouldImplement(CartContextInterface::class);
    }

    function it_always_returns_a_new_cart(FactoryInterface $cartFactory, OrderInterface $cart): void
    {
        $cartFactory->createNew()->willReturn($cart);

        $this->getCart()->shouldReturn($cart);
    }
}
