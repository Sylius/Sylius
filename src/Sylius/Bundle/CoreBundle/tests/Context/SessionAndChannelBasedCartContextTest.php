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
use Sylius\Bundle\CoreBundle\Context\SessionAndChannelBasedCartContext;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Storage\CartStorageInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;

final class SessionAndChannelBasedCartContextTest extends TestCase
{
    private CartStorageInterface&MockObject $cartStorage;

    private ChannelContextInterface&MockObject $channelContext;

    private SessionAndChannelBasedCartContext $cartContext;

    protected function setUp(): void
    {
        $this->cartStorage = $this->createMock(CartStorageInterface::class);
        $this->channelContext = $this->createMock(ChannelContextInterface::class);
        $this->cartContext = new SessionAndChannelBasedCartContext(
            $this->cartStorage,
            $this->channelContext,
        );
    }

    public function testItImplementsCartContextInterface(): void
    {
        $this->assertInstanceOf(CartContextInterface::class, $this->cartContext);
    }

    public function testItReturnsCartBasedOnIdStoredInSessionAndCurrentChannel(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $cart = $this->createMock(OrderInterface::class);

        $this->channelContext->method('getChannel')->willReturn($channel);
        $this->cartStorage->method('hasForChannel')->with($channel)->willReturn(true);
        $this->cartStorage->method('getForChannel')->with($channel)->willReturn($cart);

        $this->assertSame($cart, $this->cartContext->getCart());
    }

    public function testItThrowsCartNotFoundExceptionIfSessionKeyDoesNotExist(): void
    {
        $this->expectException(CartNotFoundException::class);

        $channel = $this->createMock(ChannelInterface::class);

        $this->channelContext->method('getChannel')->willReturn($channel);
        $this->cartStorage->method('hasForChannel')->with($channel)->willReturn(false);

        $this->cartContext->getCart();
    }

    public function testItThrowsCartNotFoundExceptionAndRemovesIdFromSessionWhenCartWasNotFound(): void
    {
        $this->expectException(CartNotFoundException::class);

        $channel = $this->createMock(ChannelInterface::class);

        $this->channelContext->method('getChannel')->willReturn($channel);
        $this->cartStorage->method('hasForChannel')->with($channel)->willReturn(true);
        $this->cartStorage->method('getForChannel')->with($channel)->willReturn(null);
        $this->cartStorage->expects($this->once())->method('removeForChannel')->with($channel);

        $this->cartContext->getCart();
    }

    public function testItThrowsCartNotFoundExceptionIfChannelWasNotFound(): void
    {
        $this->expectException(CartNotFoundException::class);

        $this->channelContext
            ->method('getChannel')
            ->willThrowException(new ChannelNotFoundException())
        ;

        $this->cartContext->getCart();
    }
}
