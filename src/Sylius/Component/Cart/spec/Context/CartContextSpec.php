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
use Prophecy\Argument;
use Sylius\Component\Cart\Context\CartContext;
use Sylius\Component\Cart\Context\CartContextInterface;
use Sylius\Component\Cart\Context\CartNotFoundException;
use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Registry\PrioritizedServiceRegistryInterface;

/**
 * @mixin CartContext
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CartContextSpec extends ObjectBehavior
{
    function let(PrioritizedServiceRegistryInterface $providersRegistry)
    {
        $this->beConstructedWith($providersRegistry);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Cart\Context\CartContext');
    }
    
    function it_implements_cart_context_interface()
    {
        $this->shouldImplement(CartContextInterface::class);
    }

    function it_uses_prioritized_service_registry_and_returns_first_obtained_cart(
        PrioritizedServiceRegistryInterface $providersRegistry,
        CartProviderInterface $firstProvider,
        CartProviderInterface $secondProvider,
        CartInterface $cart
    ) {
        $providersRegistry->all()->willReturn([$firstProvider, $secondProvider]);
        $firstProvider->getCart()->willReturn(null);
        $secondProvider->getCart()->willReturn($cart);
        
        $this->getCart()->shouldReturn($cart);
    }

    function it_throws_an_exception_if_none_of_the_providers_is_able_to_get_the_cart(
        PrioritizedServiceRegistryInterface $providersRegistry,
        CartProviderInterface $firstProvider,
        CartProviderInterface $secondProvider
    ) {
        $providersRegistry->all()->willReturn([$firstProvider, $secondProvider]);
        $firstProvider->getCart()->willReturn(null);
        $secondProvider->getCart()->willReturn(null);

        $this
            ->shouldThrow(new CartNotFoundException())
            ->during('getCart')
        ;
    }
}
