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

namespace Tests\Sylius\Bundle\CoreBundle\Context;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Context\CustomerAndChannelBasedCartContext;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;

final class CustomerAndChannelBasedCartContextTest extends TestCase
{
    private CustomerContextInterface&MockObject $customerContext;

    private ChannelContextInterface&MockObject $channelContext;

    private MockObject&OrderRepositoryInterface $orderRepository;

    private CustomerAndChannelBasedCartContext $cartContext;

    protected function setUp(): void
    {
        $this->customerContext = $this->createMock(CustomerContextInterface::class);
        $this->channelContext = $this->createMock(ChannelContextInterface::class);
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->cartContext = new CustomerAndChannelBasedCartContext(
            $this->customerContext,
            $this->channelContext,
            $this->orderRepository,
        );
    }

    public function testItImplementsCartContextInterface(): void
    {
        $this->assertInstanceOf(CartContextInterface::class, $this->cartContext);
    }

    public function testItReturnsUncompletedCartForCurrentlyLoggedUser(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $customer = $this->createMock(CustomerInterface::class);
        $order = $this->createMock(OrderInterface::class);

        $this->channelContext->method('getChannel')->willReturn($channel);
        $this->customerContext->method('getCustomer')->willReturn($customer);

        $this->orderRepository
            ->method('findLatestNotEmptyCartByChannelAndCustomer')
            ->with($channel, $customer)
            ->willReturn($order)
        ;

        $this->assertSame($order, $this->cartContext->getCart());
    }

    public function testItThrowsExceptionIfNoCartCanBeProvided(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $customer = $this->createMock(CustomerInterface::class);

        $this->channelContext->method('getChannel')->willReturn($channel);
        $this->customerContext->method('getCustomer')->willReturn($customer);

        $this->orderRepository
            ->method('findLatestNotEmptyCartByChannelAndCustomer')
            ->with($channel, $customer)
            ->willReturn(null)
        ;

        $this->expectException(CartNotFoundException::class);
        $this->expectExceptionMessage('Sylius was not able to find the cart for currently logged in user.');

        $this->cartContext->getCart();
    }

    public function testItThrowsExceptionIfThereIsNoLoggedInCustomer(): void
    {
        $channel = $this->createMock(ChannelInterface::class);

        $this->customerContext->method('getCustomer')->willReturn(null);
        $this->channelContext->method('getChannel')->willReturn($channel);

        $this->expectException(CartNotFoundException::class);
        $this->expectExceptionMessage('Sylius was not able to find the cart, as there is no logged in user.');

        $this->cartContext->getCart();
    }

    public function testItDoesNothingIfChannelCouldNotBeFound(): void
    {
        $this->channelContext->method('getChannel')->willThrowException(new ChannelNotFoundException());

        $this->expectException(CartNotFoundException::class);
        $this->expectExceptionMessage('Sylius was not able to find the cart, as there is no current channel.');

        $this->cartContext->getCart();
    }
}
