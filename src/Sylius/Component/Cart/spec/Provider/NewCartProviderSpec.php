<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Cart\Provider;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Cart\Provider\NewCartProvider;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @mixin NewCartProvider
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class NewCartProviderSpec extends ObjectBehavior
{
    function let(FactoryInterface $cartFactory)
    {
        $this->beConstructedWith($cartFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Cart\Provider\NewCartProvider');
    }

    function it_is_a_cart_provider()
    {
        $this->shouldImplement(CartProviderInterface::class);
    }

    function it_always_returns_a_new_cart(FactoryInterface $cartFactory, CartInterface $cart)
    {
        $cartFactory->createNew()->willReturn($cart);

        $this->getCart()->shouldReturn($cart);
    }
}
