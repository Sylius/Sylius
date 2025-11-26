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

namespace Tests\Sylius\Bundle\OrderBundle\Context;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\OrderBundle\Context\SessionBasedCartContext;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class SessionBasedCartContextTest extends TestCase
{
    private MockObject&SessionInterface $session;

    private MockObject&OrderRepositoryInterface $orderRepository;

    private SessionBasedCartContext $sessionBasedCartContext;

    protected function setUp(): void
    {
        parent::setUp();
        $this->session = $this->createMock(SessionInterface::class);
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->sessionBasedCartContext = new SessionBasedCartContext(
            $this->session,
            'session_key_name',
            $this->orderRepository,
        );
    }

    public function testImplementsACartContextInterface(): void
    {
        self::assertInstanceOf(CartContextInterface::class, $this->sessionBasedCartContext);
    }

    public function testReturnsACartBasedOnIdStoredInSession(): void
    {
        /** @var OrderInterface&MockObject $cart */
        $cart = $this->createMock(OrderInterface::class);

        $this->session->expects(self::once())
            ->method('has')
            ->with('session_key_name')
            ->willReturn(true);

        $this->session->expects(self::once())
            ->method('get')
            ->with('session_key_name')
            ->willReturn(12345);

        $this->orderRepository->expects(self::once())
            ->method('findCartById')
            ->with(12345)->willReturn($cart);

        $this->assertSame($cart, $this->sessionBasedCartContext->getCart());
    }

    public function testThrowsACartNotFoundExceptionIfSessionKeyDoesNotExist(): void
    {
        $this->session->expects(self::once())
            ->method('has')
            ->with('session_key_name')
            ->willReturn(false);

        self::expectException(CartNotFoundException::class);

        $this->sessionBasedCartContext->getCart();
    }

    public function testThrowsACartNotFoundExceptionAndRemovesIdFromSessionWhenCartIsNotFound(): void
    {
        $this->session->expects(self::once())
            ->method('has')
            ->with('session_key_name')
            ->willReturn(true);

        $this->session->expects(self::once())
            ->method('get')
            ->with('session_key_name')
            ->willReturn(12345);

        $this->orderRepository->expects(self::once())
            ->method('findCartById')
            ->with(12345)
            ->willReturn(null);

        $this->session->expects(self::once())
            ->method('remove')
            ->with('session_key_name')
            ->willReturn(null);

        self::expectException(CartNotFoundException::class);

        $this->sessionBasedCartContext->getCart();
    }
}
