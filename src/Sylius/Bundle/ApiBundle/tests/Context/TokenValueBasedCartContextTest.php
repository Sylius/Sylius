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

namespace Tests\Sylius\Bundle\ApiBundle\Context;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Context\TokenValueBasedCartContext;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class TokenValueBasedCartContextTest extends TestCase
{
    private MockObject&RequestStack $requestStack;

    private MockObject&OrderRepositoryInterface $orderRepository;

    private TokenValueBasedCartContext $tokenValueBasedCartContext;

    private MockObject&Request $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->tokenValueBasedCartContext = new TokenValueBasedCartContext(
            $this->requestStack,
            $this->orderRepository,
            '/api/v2',
        );
        $this->request = $this->createMock(Request::class);
    }

    public function testImplementsCartContextInterface(): void
    {
        self::assertInstanceOf(CartContextInterface::class, $this->tokenValueBasedCartContext);
    }

    public function testReturnsCartByTokenValue(): void
    {
        /** @var OrderInterface&MockObject $cart */
        $cart = $this->createMock(OrderInterface::class);

        $this->request->attributes = new ParameterBag(['tokenValue' => 'TOKEN_VALUE']);

        $this->request->expects(self::once())
            ->method('getRequestUri')
            ->willReturn('/api/v2/orders/TOKEN_VALUE');

        $this->requestStack->expects(self::once())->method('getMainRequest')->willReturn($this->request);

        $this->orderRepository->expects(self::once())
            ->method('findCartByTokenValue')
            ->with('TOKEN_VALUE')
            ->willReturn($cart);

        self::assertSame($cart, $this->tokenValueBasedCartContext->getCart());
    }

    public function testThrowsAnExceptionIfThereIsNoMasterRequestOnRequestStack(): void
    {
        $this->requestStack->expects(self::once())
            ->method('getMainRequest')
            ->willReturn(null);

        self::expectException(CartNotFoundException::class);

        self::expectExceptionMessage('There is no main request on request stack.');

        $this->tokenValueBasedCartContext->getCart();
    }

    public function testThrowsAnExceptionIfTheRequestIsNotAnApiRequest(): void
    {
        $this->request->attributes = new ParameterBag([]);
        $this->request->expects(self::once())
            ->method('getRequestUri')
            ->willReturn('/orders');

        $this->requestStack->expects(self::once())
            ->method('getMainRequest')
            ->willReturn($this->request);

        self::expectException(CartNotFoundException::class);

        self::expectExceptionMessage('The main request is not an API request.');

        $this->tokenValueBasedCartContext->getCart();
    }

    public function testThrowsAnExceptionIfThereIsNoTokenValue(): void
    {
        $this->request->attributes = new ParameterBag([]);
        $this->request->expects(self::once())
            ->method('getRequestUri')
            ->willReturn('/api/v2/orders');

        $this->requestStack->expects(self::once())
            ->method('getMainRequest')
            ->willReturn($this->request);

        self::expectException(CartNotFoundException::class);

        self::expectExceptionMessage('Sylius was not able to find the cart, as there is no passed token value.');

        $this->tokenValueBasedCartContext->getCart();
    }

    public function testThrowsAnExceptionIfThereIsNoCartWithGivenTokenValue(): void
    {
        $this->request->attributes = new ParameterBag(['tokenValue' => 'TOKEN_VALUE']);
        $this->request->expects(self::once())
            ->method('getRequestUri')
            ->willReturn('/api/v2/orders/TOKEN_VALUE');

        $this->requestStack->expects(self::once())
            ->method('getMainRequest')
            ->willReturn($this->request);

        $this->orderRepository->expects(self::once())
            ->method('findCartByTokenValue')
            ->with('TOKEN_VALUE')
            ->willReturn(null);

        self::expectException(CartNotFoundException::class);

        self::expectExceptionMessage('Sylius was not able to find the cart for passed token value.');

        $this->tokenValueBasedCartContext->getCart();
    }
}
