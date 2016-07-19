<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Cart\Provider;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Cart\Provider\NewCartProvider;
use Sylius\Component\Core\Context\ShopperContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;

/**
 * @mixin NewCartProvider
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class NewCartProviderSpec extends ObjectBehavior
{
    function let(CartProviderInterface $decoratedCartProvider, ShopperContextInterface $shopperContext)
    {
        $this->beConstructedWith($decoratedCartProvider, $shopperContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Cart\Provider\NewCartProvider');
    }

    function it_implements_cart_provider_interface()
    {
        $this->shouldImplement(CartProviderInterface::class);
    }

    function it_decorates_a_standard_new_cart_provider_and_adds_context_to_the_cart(
        CartProviderInterface $decoratedCartProvider,
        OrderInterface $cart,
        ShopperContextInterface $shopperContext,
        ChannelInterface $channel,
        CustomerInterface $customer
    ) {
        $decoratedCartProvider->getCart()->willReturn($cart);

        $shopperContext->getChannel()->willReturn($channel);
        $shopperContext->getCurrencyCode()->willReturn('PLN');
        $shopperContext->getLocaleCode()->willReturn('pl_PL');
        $shopperContext->getCustomer()->willReturn($customer);

        $cart->setChannel($channel)->shouldBeCalled();
        $cart->setCurrencyCode('PLN')->shouldBeCalled();
        //$cart->setLocale('pl_PL')->shouldBeCalled();
        $cart->setCustomer($customer)->shouldBeCalled();

        $this->getCart()->shouldReturn($cart);
    }

    function it_does_not_assign_the_customer_if_undefined(
        CartProviderInterface $decoratedCartProvider,
        OrderInterface $cart,
        ShopperContextInterface $shopperContext,
        ChannelInterface $channel
    ) {
        $decoratedCartProvider->getCart()->willReturn($cart);

        $shopperContext->getChannel()->willReturn($channel);
        $shopperContext->getCurrencyCode()->willReturn('PLN');
        $shopperContext->getLocaleCode()->willReturn('pl_PL');
        $shopperContext->getCustomer()->willReturn(null);

        $cart->setChannel($channel)->shouldBeCalled();
        $cart->setCurrencyCode('PLN')->shouldBeCalled();
        $cart->setCustomer(null)->shouldBeCalled();
        
        $this->getCart()->shouldReturn($cart);
    }

    function it_throws_an_exception_if_decorated_provider_returns_null(CartProviderInterface $decoratedCartProvider)
    {
        $decoratedCartProvider->getCart()->willReturn(null);

        $this
            ->shouldThrow(\LogicException::class)
            ->during('getCart')
        ;
    }

    function it_does_not_assign_the_channel_if_undefined(
        CartProviderInterface $decoratedCartProvider,
        OrderInterface $cart,
        ShopperContextInterface $shopperContext
    ) {
        $decoratedCartProvider->getCart()->willReturn($cart);

        $shopperContext->getChannel()->willThrow(ChannelNotFoundException::class);
        $shopperContext->getCurrencyCode()->willReturn('PLN');
        $shopperContext->getLocaleCode()->willReturn('pl_PL');
        $shopperContext->getCustomer()->willReturn(null);

        $cart->setChannel()->shouldBeCalled();
        $cart->setCurrencyCode('PLN')->shouldBeCalled();
        $cart->setCustomer(null)->shouldBeCalled();

        $this->getCart()->shouldReturn($cart);
    }
}
