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
use Prophecy\Argument;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Cart\Resolver\CreatedByGuestFlagResolverInterface;
use Sylius\Component\Core\Context\ShopperContextInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;

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
        CustomerInterface $customer,
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
        CustomerInterface $customer,
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
        $cart->setBillingAddress(Argument::that(static function (AddressInterface $address): bool {
            return $address->getCustomer() === null;
        }))->shouldBeCalled();

        $this->getCart()->shouldReturn($cart);
    }

    function it_throws_a_cart_not_found_exception_if_channel_is_undefined(
        CartContextInterface $cartContext,
        ShopperContextInterface $shopperContext,
        OrderInterface $cart,
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
        OrderInterface $cart,
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

    function it_caches_a_cart(
        CartContextInterface $cartContext,
        ShopperContextInterface $shopperContext,
        OrderInterface $cart,
        ChannelInterface $channel,
        CurrencyInterface $currency,
        CustomerInterface $customer,
    ): void {
        $cartContext->getCart()->shouldBeCalledTimes(1)->willReturn($cart);

        $shopperContext->getChannel()->shouldBeCalledTimes(1)->willReturn($channel);
        $shopperContext->getLocaleCode()->shouldBeCalledTimes(1)->willReturn('pl');
        $shopperContext->getCustomer()->shouldBeCalledTimes(1)->willReturn($customer);
        $customer->getDefaultAddress()->shouldBeCalledTimes(1)->willReturn(null);

        $channel->getBaseCurrency()->shouldBeCalledTimes(1)->willReturn($currency);
        $currency->getCode()->shouldBeCalledTimes(1)->willReturn('PLN');

        $cart->setChannel($channel)->shouldBeCalledTimes(1);
        $cart->setCurrencyCode('PLN')->shouldBeCalledTimes(1);
        $cart->setLocaleCode('pl')->shouldBeCalledTimes(1);
        $cart->setCustomer($customer)->shouldBeCalledTimes(1);

        $this->getCart()->shouldReturn($cart);
        $this->getCart()->shouldReturn($cart);
    }

    function it_recreates_a_cart_after_it_is_reset(
        CartContextInterface $cartContext,
        ShopperContextInterface $shopperContext,
        OrderInterface $firstCart,
        OrderInterface $secondCart,
        ChannelInterface $channel,
        CurrencyInterface $currency,
        CustomerInterface $customer,
    ): void {
        $cartContext->getCart()->shouldBeCalledTimes(2)->willReturn($firstCart, $secondCart);

        $shopperContext->getChannel()->shouldBeCalledTimes(2)->willReturn($channel);
        $shopperContext->getLocaleCode()->shouldBeCalledTimes(2)->willReturn('pl');
        $shopperContext->getCustomer()->shouldBeCalledTimes(2)->willReturn($customer);
        $customer->getDefaultAddress()->shouldBeCalledTimes(2)->willReturn(null);

        $channel->getBaseCurrency()->shouldBeCalledTimes(2)->willReturn($currency);
        $currency->getCode()->shouldBeCalledTimes(2)->willReturn('PLN');

        $firstCart->setChannel($channel)->shouldBeCalledTimes(1);
        $firstCart->setCurrencyCode('PLN')->shouldBeCalledTimes(1);
        $firstCart->setLocaleCode('pl')->shouldBeCalledTimes(1);
        $firstCart->setCustomer($customer)->shouldBeCalledTimes(1);

        $secondCart->setChannel($channel)->shouldBeCalledTimes(1);
        $secondCart->setCurrencyCode('PLN')->shouldBeCalledTimes(1);
        $secondCart->setLocaleCode('pl')->shouldBeCalledTimes(1);
        $secondCart->setCustomer($customer)->shouldBeCalledTimes(1);

        $this->getCart()->shouldReturn($firstCart);
        $this->reset();
        $this->getCart()->shouldReturn($secondCart);
    }

    function it_creates_order_for_authorized_user(
        CartContextInterface $cartContext,
        ShopperContextInterface $shopperContext,
        CreatedByGuestFlagResolverInterface $createdByGuestFlagResolver,
        OrderInterface $cart,
        ChannelInterface $channel,
        CurrencyInterface $currency,
        CustomerInterface $customer,
    ): void {
        $this->beConstructedWith($cartContext, $shopperContext, $createdByGuestFlagResolver);

        $createdByGuestFlagResolver->resolveFlag()->willReturn(false);

        $cartContext->getCart()->shouldBeCalledTimes(1)->willReturn($cart);

        $shopperContext->getChannel()->willReturn($channel);
        $shopperContext->getLocaleCode()->willReturn('pl');
        $shopperContext->getCustomer()->willReturn($customer);
        $customer->getDefaultAddress()->willReturn(null);

        $channel->getBaseCurrency()->willReturn($currency);
        $currency->getCode()->willReturn('PLN');

        $cart->setChannel($channel)->shouldBeCalled();
        $cart->setCurrencyCode('PLN')->shouldBeCalled();
        $cart->setLocaleCode('pl')->shouldBeCalled();
        $cart->setCustomerWithAuthorization($customer)->shouldBeCalled();

        $this->getCart()->shouldReturn($cart);
    }
}
