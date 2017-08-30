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

namespace spec\Sylius\Component\Core\Cart\Context;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Context\ShopperContextInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ShopBasedCartContextSpec extends ObjectBehavior
{
    function let(CartContextInterface $cartContext, ShopperContextInterface $shopperContext): void
    {
        $this->beConstructedWith($cartContext, $shopperContext);
    }

    function it_implements_a_cart_context_interface(): void
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
    ): void {
        $cartContext->getCart()->willReturn($cart);

        $shopperContext->getChannel()->willReturn($channel);
        $shopperContext->getLocaleCode()->willReturn('pl');
        $shopperContext->getCustomer()->willReturn($customer);
        $customer->getDefaultAddress()->willReturn(null);

        $channel->getBaseCurrency()->willReturn($currency);
        $currency->getCode()->willReturn('PLN');

        $cart->setChannel($channel)->shouldBeCalled();
        $cart->setCurrencyCode('PLN')->shouldBeCalled();
        $cart->setLocaleCode('pl')->shouldBeCalled();
        $cart->setCustomer($customer)->shouldBeCalled();

        $this->getCart()->shouldReturn($cart);
    }

    function it_creates_a_cart_if_does_not_exist_with_shop_basic_configuration_and_customer_default_address_if_is_not_null(
        CartContextInterface $cartContext,
        ShopperContextInterface $shopperContext,
        AddressInterface $defaultAddress,
        OrderInterface $cart,
        ChannelInterface $channel,
        CurrencyInterface $currency,
        CustomerInterface $customer
    ): void {
        $cartContext->getCart()->willReturn($cart);

        $shopperContext->getChannel()->willReturn($channel);
        $shopperContext->getLocaleCode()->willReturn('pl');
        $shopperContext->getCustomer()->willReturn($customer);
        $customer->getDefaultAddress()->willReturn($defaultAddress);

        $channel->getBaseCurrency()->willReturn($currency);
        $currency->getCode()->willReturn('PLN');

        $cart->setChannel($channel)->shouldBeCalled();
        $cart->setCurrencyCode('PLN')->shouldBeCalled();
        $cart->setLocaleCode('pl')->shouldBeCalled();
        $cart->setCustomer($customer)->shouldBeCalled();
        $cart->setShippingAddress($defaultAddress)->shouldBeCalled();

        $this->getCart()->shouldReturn($cart);
    }

    function it_throws_a_cart_not_found_exception_if_channel_is_undefined(
        CartContextInterface $cartContext,
        ShopperContextInterface $shopperContext,
        OrderInterface $cart
    ): void {
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
    ): void {
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
