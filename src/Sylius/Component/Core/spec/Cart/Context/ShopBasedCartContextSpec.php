<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Cart\Context;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Cart\Context\ShopBasedCartContext;
use Sylius\Component\Core\Context\ShopperContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Currency\Context\CurrencyNotFoundException;
use Sylius\Component\Locale\Context\LocaleNotFoundException;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ShopBasedCartContextSpec extends ObjectBehavior
{
    function let(CartContextInterface $cartContext, ShopperContextInterface $shopperContext)
    {
        $this->beConstructedWith($cartContext, $shopperContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ShopBasedCartContext::class);
    }

    function it_implements_a_cart_context_interface()
    {
        $this->shouldImplement(CartContextInterface::class);
    }

    function it_creates_a_cart_if_does_not_exist_with_shop_basic_configuration(
        CartContextInterface $cartContext,
        ShopperContextInterface $shopperContext,
        OrderInterface $cart,
        ChannelInterface $channel,
        CurrencyInterface $currency,
        CustomerInterface $customer
    ) {
        $cartContext->getCart()->willReturn($cart);

        $shopperContext->getChannel()->willReturn($channel);
        $shopperContext->getLocaleCode()->willReturn('pl');
        $shopperContext->getCustomer()->willReturn($customer);

        $channel->getBaseCurrency()->willReturn($currency);
        $currency->getCode()->willReturn('PLN');

        $cart->setChannel($channel)->shouldBeCalled();
        $cart->setCurrencyCode('PLN')->shouldBeCalled();
        $cart->setLocaleCode('pl')->shouldBeCalled();
        $cart->setCustomer($customer)->shouldBeCalled();

        $this->getCart()->shouldReturn($cart);
    }

    function it_throws_a_cart_not_found_exception_if_channel_is_undefined(
        CartContextInterface $cartContext,
        ShopperContextInterface $shopperContext,
        OrderInterface $cart
    ) {
        $cartContext->getCart()->willReturn($cart);
        $shopperContext->getChannel()->willThrow(ChannelNotFoundException::class);

        $this
            ->shouldThrow(CartNotFoundException::class)
            ->during('getCart')
        ;
    }

    function it_throws_a_cart_not_found_exception_if_locale_code_is_undefined(
        CartContextInterface $cartContext,
        ShopperContextInterface $shopperContext,
        ChannelInterface $channel,
        CurrencyInterface $currency,
        OrderInterface $cart
    ) {
        $cartContext->getCart()->willReturn($cart);
        $shopperContext->getChannel()->willReturn($channel);
        $channel->getBaseCurrency()->willReturn($currency);
        $currency->getCode()->willReturn('PLN');
        $shopperContext->getLocaleCode()->willThrow(LocaleNotFoundException::class);

        $this
            ->shouldThrow(CartNotFoundException::class)
            ->during('getCart')
        ;
    }
}
