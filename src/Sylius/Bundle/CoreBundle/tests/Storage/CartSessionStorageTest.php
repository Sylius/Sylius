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

namespace Tests\Sylius\Bundle\CoreBundle\Storage;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Storage\CartSessionStorage;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Storage\CartStorageInterface;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class CartSessionStorageTest extends TestCase
{
    private MockObject&RequestStack $requestStack;

    private MockObject&OrderRepositoryInterface $orderRepository;

    private CartSessionStorage $cartSessionStorage;

    protected function setUp(): void
    {
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);

        $this->cartSessionStorage = new CartSessionStorage(
            $this->requestStack,
            'session_key_name',
            $this->orderRepository,
        );
    }

    public function testImplementsCartStorageInterface(): void
    {
        $this->assertInstanceOf(CartStorageInterface::class, $this->cartSessionStorage);
    }

    public function testReturnsFalseWhenSessionNotFound(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $channel->method('getCode')->willReturn('channel_code');

        $this->requestStack->method('getSession')->willThrowException(new SessionNotFoundException());

        $this->assertFalse($this->cartSessionStorage->hasForChannel($channel));
    }

    public function testChecksIfCartIsInSession(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $channel->method('getCode')->willReturn('channel_code');

        $session = $this->createMock(SessionInterface::class);
        $this->requestStack->method('getSession')->willReturn($session);

        $session->method('has')->with('session_key_name.channel_code')->willReturn(true);

        $this->assertTrue($this->cartSessionStorage->hasForChannel($channel));
    }

    public function testSetsCartInSession(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $channel->method('getCode')->willReturn('channel_code');

        $cart = $this->createMock(OrderInterface::class);
        $cart->method('getId')->willReturn(14);

        $session = $this->createMock(SessionInterface::class);
        $this->requestStack->method('getSession')->willReturn($session);

        $session->expects($this->once())->method('set')->with('session_key_name.channel_code', 14);

        $this->cartSessionStorage->setForChannel($channel, $cart);
    }

    public function testGetsCartFromSession(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $channel->method('getCode')->willReturn('channel_code');

        $cart = $this->createMock(OrderInterface::class);
        $session = $this->createMock(SessionInterface::class);

        $this->requestStack->method('getSession')->willReturn($session);
        $session->method('has')->with('session_key_name.channel_code')->willReturn(true);
        $session->method('get')->with('session_key_name.channel_code')->willReturn(14);
        $this->orderRepository->method('findCartByChannel')->with(14, $channel)->willReturn($cart);

        $this->assertSame($cart, $this->cartSessionStorage->getForChannel($channel));
    }

    public function testRemovesCartFromSession(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $channel->method('getCode')->willReturn('channel_code');

        $session = $this->createMock(SessionInterface::class);
        $this->requestStack->method('getSession')->willReturn($session);

        $session->expects($this->once())->method('remove')->with('session_key_name.channel_code');

        $this->cartSessionStorage->removeForChannel($channel);
    }
}
