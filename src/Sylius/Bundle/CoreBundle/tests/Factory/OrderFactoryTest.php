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

namespace Tests\Sylius\Bundle\CoreBundle\Factory;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Factory\OrderFactory;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Resource\Factory\FactoryInterface;

final class OrderFactoryTest extends TestCase
{
    private FactoryInterface&MockObject $baseFactory;

    private OrderFactory $orderFactory;

    protected function setUp(): void
    {
        $this->baseFactory = $this->createMock(FactoryInterface::class);
        $this->orderFactory = new OrderFactory($this->baseFactory);
    }

    public function testCreatesAnOrder(): void
    {
        $order = $this->createMock(OrderInterface::class);

        $this->baseFactory->expects($this->once())->method('createNew')->willReturn($order);

        $this->assertSame($order, $this->orderFactory->createNew());
    }

    public function testCreatesACart(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $currency = $this->createMock(CurrencyInterface::class);
        $channel = $this->createMock(ChannelInterface::class);

        $this->baseFactory->expects($this->once())->method('createNew')->willReturn($order);
        $order->expects($this->once())->method('setState')->with(OrderInterface::STATE_CART);

        $order->setChannel($channel);

        $order->expects($this->once())->method('setLocaleCode')->with('en_US');
        $order->expects($this->once())->method('setCurrencyCode')->with('USD');
        $currency->expects($this->once())->method('getCode')->willReturn('USD');
        $channel->expects($this->once())->method('getBaseCurrency')->willReturn($currency);

        $cart = $this->orderFactory->createNewCart(
            $channel,
            null,
            'en_US',
        );

        $this->assertInstanceOf(OrderInterface::class, $cart);
    }

    public function testCreatesACartWithCustomer(): void
    {
        $address = $this->createMock(AddressInterface::class);
        $order = $this->createMock(OrderInterface::class);
        $currency = $this->createMock(CurrencyInterface::class);
        $channel = $this->createMock(ChannelInterface::class);
        $customer = $this->createMock(CustomerInterface::class);

        $this->baseFactory->expects($this->once())->method('createNew')->willReturn($order);
        $order->expects($this->once())->method('setState')->with(OrderInterface::STATE_CART);

        $order->setChannel($channel);

        $order->expects($this->once())->method('setLocaleCode')->with('en_US');
        $order->expects($this->once())->method('setCurrencyCode')->with('USD');
        $order->expects($this->once())->method('setCustomerWithAuthorization')->with($customer);
        $order->expects($this->once())->method('setBillingAddress')->with($address);
        $currency->expects($this->once())->method('getCode')->willReturn('USD');
        $channel->expects($this->once())->method('getBaseCurrency')->willReturn($currency);
        $customer->expects($this->once())->method('getDefaultAddress')->willReturn($address);

        $cart = $this->orderFactory->createNewCart(
            $channel,
            $customer,
            'en_US',
        );

        $this->assertInstanceOf(OrderInterface::class, $cart);
    }

    public function testCreatesACartWithToken(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $currency = $this->createMock(CurrencyInterface::class);
        $channel = $this->createMock(ChannelInterface::class);

        $this->baseFactory->expects($this->once())->method('createNew')->willReturn($order);
        $order->expects($this->once())->method('setState')->with(OrderInterface::STATE_CART);

        $order->setChannel($channel);

        $order->expects($this->once())->method('setLocaleCode')->with('en_US');
        $order->expects($this->once())->method('setCurrencyCode')->with('USD');
        $order->expects($this->once())->method('setTokenValue')->with('mytoken');
        $currency->expects($this->once())->method('getCode')->willReturn('USD');
        $channel->expects($this->once())->method('getBaseCurrency')->willReturn($currency);

        $cart = $this->orderFactory->createNewCart(
            $channel,
            null,
            'en_US',
            'mytoken',
        );

        $this->assertInstanceOf(OrderInterface::class, $cart);
    }
}
