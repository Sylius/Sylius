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
use Sylius\Component\Cart\Context\CartContextInterface;
use Sylius\Component\Cart\Context\CartNotFoundException;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Context\ShopperContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ShopBasedCartContextSpec extends ObjectBehavior
{
    function let(FactoryInterface $cartFactory, ShopperContextInterface $shopperContext)
    {
        $this->beConstructedWith($cartFactory, $shopperContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Cart\Context\ShopBasedCartContext');
    }

    function it_implements_cart_context_interface()
    {
        $this->shouldImplement(CartContextInterface::class);
    }

    function it_creates_cart_if_does_not_exist_with_shop_basic_configuration(
        FactoryInterface $cartFactory,
        ShopperContextInterface $shopperContext,
        OrderInterface $cart,
        ChannelInterface $channel,
        CustomerInterface $customer
    ) {
        $cartFactory->createNew()->willReturn($cart);

        $shopperContext->getChannel()->willReturn($channel);
        $shopperContext->getCurrencyCode()->willReturn('PLN');
        $shopperContext->getCustomer()->willReturn($customer);

        $cart->setChannel($channel)->shouldBeCalled();
        $cart->setCurrencyCode('PLN')->shouldBeCalled();
        $cart->setCustomer($customer)->shouldBeCalled();

        $this->getCart()->shouldReturn($cart);
    }

    function it_throws_cart_not_found_exception_if_channel_is_undefined(
        FactoryInterface $cartFactory,
        ShopperContextInterface $shopperContext,
        OrderInterface $cart
    ) {
        $cartFactory->createNew()->willReturn($cart);
        $shopperContext->getChannel()->willThrow(ChannelNotFoundException::class);

        $this
            ->shouldThrow(CartNotFoundException::class)
            ->during('getCart')
        ;
    }

    function it_throws_cart_not_found_exception_if_currency_code_is_undefined(
        FactoryInterface $cartFactory,
        ShopperContextInterface $shopperContext,
        ChannelInterface $channel,
        OrderInterface $cart
    ) {
        $cartFactory->createNew()->willReturn($cart);
        $shopperContext->getChannel()->willReturn($channel);
        $shopperContext->getCurrencyCode()->willThrow(ChannelNotFoundException::class);

        $this
            ->shouldThrow(CartNotFoundException::class)
            ->during('getCart')
        ;
    }
}
