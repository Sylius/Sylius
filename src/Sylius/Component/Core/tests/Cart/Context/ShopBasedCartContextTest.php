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

namespace Tests\Sylius\Component\Core\Cart\Context;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Cart\Context\ShopBasedCartContext;
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
use Symfony\Contracts\Service\ResetInterface;

final class ShopBasedCartContextTest extends TestCase
{
    private CartContextInterface&MockObject $cartContext;

    private MockObject&ShopperContextInterface $shopperContext;

    private CreatedByGuestFlagResolverInterface&MockObject $createdByGuestFlagResolver;

    private MockObject&OrderInterface $cart;

    private ChannelInterface&MockObject $channel;

    private CurrencyInterface&MockObject $currency;

    private CustomerInterface&MockObject $customer;

    private ShopBasedCartContext $shopBasedCartContext;

    protected function setUp(): void
    {
        $this->cartContext = $this->createMock(CartContextInterface::class);
        $this->shopperContext = $this->createMock(ShopperContextInterface::class);
        $this->createdByGuestFlagResolver = $this->createMock(CreatedByGuestFlagResolverInterface::class);
        $this->cart = $this->createMock(OrderInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->currency = $this->createMock(CurrencyInterface::class);
        $this->customer = $this->createMock(CustomerInterface::class);
        $this->shopBasedCartContext = new ShopBasedCartContext(
            $this->cartContext,
            $this->shopperContext,
            $this->createdByGuestFlagResolver,
        );
    }

    public function testShouldImplementCartContextInterface(): void
    {
        $this->assertInstanceOf(CartContextInterface::class, $this->shopBasedCartContext);
    }

    public function testShouldImplementResetInterface(): void
    {
        $this->assertInstanceOf(ResetInterface::class, $this->shopBasedCartContext);
    }

    public function testShouldCreateCartIfDoesNotExistWithShopBasicConfiguration(): void
    {
        $this->cartContext->expects($this->once())->method('getCart')->willReturn($this->cart);
        $this->shopperContext->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->shopperContext->expects($this->once())->method('getLocaleCode')->willReturn('pl');
        $this->shopperContext->expects($this->once())->method('getCustomer')->willReturn($this->customer);
        $this->customer->expects($this->once())->method('getDefaultAddress')->willReturn(null);
        $this->channel->expects($this->once())->method('getBaseCurrency')->willReturn($this->currency);
        $this->currency->expects($this->once())->method('getCode')->willReturn('PLN');
        $this->createdByGuestFlagResolver->expects($this->once())->method('resolveFlag')->willReturn(true);
        $this->cart->expects($this->once())->method('setChannel')->with($this->channel);
        $this->cart->expects($this->once())->method('setCurrencyCode')->with('PLN');
        $this->cart->expects($this->once())->method('setLocaleCode')->with('pl');
        $this->cart->expects($this->once())->method('setCustomer')->with($this->customer);

        $this->assertSame($this->cart, $this->shopBasedCartContext->getCart());
    }

    public function testShouldCreateCartIfDoesNotExistWithShopBasicConfigurationAndCustomerDefaultAddressIfIsNotNull(): void
    {
        $defaultAddress = $this->createMock(AddressInterface::class);
        $this->cartContext->expects($this->once())->method('getCart')->willReturn($this->cart);
        $this->shopperContext->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->shopperContext->expects($this->once())->method('getLocaleCode')->willReturn('pl');
        $this->shopperContext->expects($this->once())->method('getCustomer')->willReturn($this->customer);
        $this->customer->expects($this->once())->method('getDefaultAddress')->willReturn($defaultAddress);
        $this->channel->expects($this->once())->method('getBaseCurrency')->willReturn($this->currency);
        $this->currency->expects($this->once())->method('getCode')->willReturn('PLN');
        $this->createdByGuestFlagResolver->expects($this->once())->method('resolveFlag')->willReturn(true);
        $this->cart->expects($this->once())->method('setChannel')->with($this->channel);
        $this->cart->expects($this->once())->method('setCurrencyCode')->with('PLN');
        $this->cart->expects($this->once())->method('setLocaleCode')->with('pl');
        $this->cart->expects($this->once())->method('setCustomer')->with($this->customer);
        $this->cart->expects($this->once())
            ->method('setBillingAddress')
            ->with($this->callback(static fn (AddressInterface $address): bool => $address->getCustomer() === null));

        $this->assertSame($this->cart, $this->shopBasedCartContext->getCart());
    }

    public function testShouldThrowCartNotFoundExceptionIfChannelIsUndefined(): void
    {
        $this->expectException(CartNotFoundException::class);
        $this->cartContext->expects($this->once())->method('getCart')->willReturn($this->cart);
        $this->shopperContext->expects($this->once())->method('getChannel')->willThrowException(new ChannelNotFoundException());

        $this->shopBasedCartContext->getCart();
    }

    public function testThrowCartNotFoundExceptionIfLocaleCodeIsUndefined(): void
    {
        $this->expectException(CartNotFoundException::class);
        $this->cartContext->expects($this->once())->method('getCart')->willReturn($this->cart);
        $this->shopperContext->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('getBaseCurrency')->willReturn($this->currency);
        $this->currency->expects($this->once())->method('getCode')->willReturn('PLN');
        $this->shopperContext->expects($this->once())->method('getLocaleCode')->willThrowException(new LocaleNotFoundException());

        $this->shopBasedCartContext->getCart();
    }

    public function testShouldCacheCart(): void
    {
        $this->cartContext->expects($this->once())->method('getCart')->willReturn($this->cart);
        $this->shopperContext->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->shopperContext->expects($this->once())->method('getLocaleCode')->willReturn('pl');
        $this->shopperContext->expects($this->once())->method('getCustomer')->willReturn($this->customer);
        $this->customer->expects($this->once())->method('getDefaultAddress')->willReturn(null);
        $this->channel->expects($this->once())->method('getBaseCurrency')->willReturn($this->currency);
        $this->currency->expects($this->once())->method('getCode')->willReturn('PLN');
        $this->createdByGuestFlagResolver->expects($this->once())->method('resolveFlag')->willReturn(true);
        $this->cart->expects($this->once())->method('setChannel')->with($this->channel);
        $this->cart->expects($this->once())->method('setCurrencyCode')->with('PLN');
        $this->cart->expects($this->once())->method('setLocaleCode')->with('pl');
        $this->cart->expects($this->once())->method('setCustomer')->with($this->customer);

        $this->shopBasedCartContext->getCart();
        $this->shopBasedCartContext->getCart();
    }

    public function testShouldRecreateCartAfterReset(): void
    {
        $secondCart = $this->createMock(OrderInterface::class);
        $this->cartContext->expects($this->exactly(2))->method('getCart')->willReturnOnConsecutiveCalls(
            $this->cart,
            $secondCart,
        );
        $this->shopperContext->expects($this->exactly(2))->method('getChannel')->willReturn($this->channel);
        $this->shopperContext->expects($this->exactly(2))->method('getLocaleCode')->willReturn('pl');
        $this->shopperContext->expects($this->exactly(2))->method('getCustomer')->willReturn($this->customer);
        $this->customer->expects($this->exactly(2))->method('getDefaultAddress')->willReturn(null);
        $this->channel->expects($this->exactly(2))->method('getBaseCurrency')->willReturn($this->currency);
        $this->currency->expects($this->exactly(2))->method('getCode')->willReturn('PLN');
        $this->createdByGuestFlagResolver->expects($this->exactly(2))->method('resolveFlag')->willReturn(true);
        $this->cart->expects($this->once())->method('setChannel')->with($this->channel);
        $this->cart->expects($this->once())->method('setCurrencyCode')->with('PLN');
        $this->cart->expects($this->once())->method('setLocaleCode')->with('pl');
        $this->cart->expects($this->once())->method('setCustomer')->with($this->customer);
        $secondCart->expects($this->once())->method('setChannel')->with($this->channel);
        $secondCart->expects($this->once())->method('setCurrencyCode')->with('PLN');
        $secondCart->expects($this->once())->method('setLocaleCode')->with('pl');
        $secondCart->expects($this->once())->method('setCustomer')->with($this->customer);

        $this->assertSame($this->cart, $this->shopBasedCartContext->getCart());
        $this->shopBasedCartContext->reset();
        $this->assertSame($secondCart, $this->shopBasedCartContext->getCart());
    }

    public function testShouldCreateOrderForAuthorizedUser(): void
    {
        $this->createdByGuestFlagResolver->expects($this->once())->method('resolveFlag')->willReturn(false);
        $this->cartContext->expects($this->once())->method('getCart')->willReturn($this->cart);
        $this->shopperContext->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->shopperContext->expects($this->once())->method('getLocaleCode')->willReturn('pl');
        $this->shopperContext->expects($this->once())->method('getCustomer')->willReturn($this->customer);
        $this->customer->expects($this->once())->method('getDefaultAddress')->willReturn(null);
        $this->channel->expects($this->once())->method('getBaseCurrency')->willReturn($this->currency);
        $this->currency->expects($this->once())->method('getCode')->willReturn('PLN');
        $this->cart->expects($this->once())->method('setChannel')->with($this->channel);
        $this->cart->expects($this->once())->method('setCurrencyCode')->with('PLN');
        $this->cart->expects($this->once())->method('setLocaleCode')->with('pl');
        $this->cart->expects($this->once())->method('setCustomerWithAuthorization')->with($this->customer);

        $this->assertSame($this->cart, $this->shopBasedCartContext->getCart());
    }
}
