<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class OrderFactorySpec extends ObjectBehavior
{
    public function let(FactoryInterface $baseFactory): void
    {
        $this->beConstructedWith($baseFactory);
    }

    public function it_creates_an_order(OrderInterface $order, FactoryInterface $baseFactory): void
    {
        $baseFactory->createNew()->willReturn($order);

        $this->createNew()->shouldReturn($order);
    }

    public function it_creates_a_cart(
        OrderInterface $order,
        AddressInterface $address,
        CurrencyInterface $currency,
        ChannelInterface $channel,
        FactoryInterface $baseFactory,
    ): void {
        $baseFactory->createNew()->willReturn($order);

        $order->setState(OrderInterface::STATE_CART)->shouldBeCalled();
        $order->setChannel($channel);
        $order->setLocaleCode('en_US')->shouldBeCalled();
        $order->setCurrencyCode('USD')->shouldBeCalled();

        $currency->getCode()->willReturn('USD');
        $channel->getBaseCurrency()->willReturn($currency);

        $cart = $this->createNewCart(
            $channel->getWrappedObject(),
            null,
            'en_US',
        );

        $cart->shouldBeAnInstanceOf(OrderInterface::class);
    }

    public function it_creates_a_cart_with_customer(
        AddressInterface $address,
        OrderInterface $order,
        CurrencyInterface $currency,
        ChannelInterface $channel,
        CustomerInterface $customer,
        FactoryInterface $baseFactory,
    ): void {
        $baseFactory->createNew()->willReturn($order);

        $order->setState(OrderInterface::STATE_CART)->shouldBeCalled();
        $order->setChannel($channel);
        $order->setLocaleCode('en_US')->shouldBeCalled();
        $order->setCurrencyCode('USD')->shouldBeCalled();
        $order->setCustomerWithAuthorization($customer)->shouldBeCalled();
        $order->setBillingAddress($address)->shouldBeCalled();

        $currency->getCode()->willReturn('USD');
        $channel->getBaseCurrency()->willReturn($currency);
        $customer->getDefaultAddress()->willReturn($address);

        $cart = $this->createNewCart(
            $channel->getWrappedObject(),
            $customer->getWrappedObject(),
            'en_US',
        );

        $cart->shouldBeAnInstanceOf(OrderInterface::class);
    }

    public function it_creates_a_cart_with_token(
        OrderInterface $order,
        CurrencyInterface $currency,
        ChannelInterface $channel,
        FactoryInterface $baseFactory,
    ): void {
        $baseFactory->createNew()->willReturn($order);

        $order->setState(OrderInterface::STATE_CART)->shouldBeCalled();
        $order->setChannel($channel);
        $order->setLocaleCode('en_US')->shouldBeCalled();
        $order->setCurrencyCode('USD')->shouldBeCalled();
        $order->setTokenValue('mytoken')->shouldBeCalled();

        $currency->getCode()->willReturn('USD');
        $channel->getBaseCurrency()->willReturn($currency);

        $cart = $this->createNewCart(
            $channel->getWrappedObject(),
            null,
            'en_US',
            'mytoken',
        );

        $cart->shouldBeAnInstanceOf(OrderInterface::class);
    }
}
