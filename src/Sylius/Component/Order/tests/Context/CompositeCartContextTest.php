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

namespace Tests\Sylius\Component\Order\Context;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Sylius\Component\Order\Context\CompositeCartContext;
use Sylius\Component\Order\Model\OrderInterface;

final class CompositeCartContextTest extends TestCase
{
    private CompositeCartContext $context;

    private MockObject&OrderInterface $cart;

    private CartContextInterface&MockObject $cartContext1;

    private CartContextInterface&MockObject $cartContext2;

    protected function setUp(): void
    {
        $this->context = new CompositeCartContext();
        $this->cart = $this->createMock(OrderInterface::class);
        $this->cartContext1 = $this->createMock(CartContextInterface::class);
        $this->cartContext2 = $this->createMock(CartContextInterface::class);
    }

    public function testItImplementsCartContextInterface(): void
    {
        $this->assertInstanceOf(CartContextInterface::class, $this->context);
    }

    public function testItThrowsExceptionIfNoNestedContextsDefined(): void
    {
        $this->expectException(CartNotFoundException::class);

        $this->context->getCart();
    }

    public function testItThrowsExceptionIfNoContextReturnsCart(): void
    {
        $this->expectException(CartNotFoundException::class);

        $this->cartContext1->expects($this->once())->method('getCart')->willThrowException(new CartNotFoundException());

        $this->context->addContext($this->cartContext1);
        $this->context->getCart();
    }

    public function testItReturnsCartFromFirstAvailableContext(): void
    {
        $this->cartContext1->expects($this->once())->method('getCart')->willThrowException(new CartNotFoundException());
        $this->cartContext2->expects($this->once())->method('getCart')->willReturn($this->cart);

        $this->context->addContext($this->cartContext1);
        $this->context->addContext($this->cartContext2);

        $this->assertSame($this->cart, $this->context->getCart());
    }

    public function testContextsCanHavePriority(): void
    {
        $this->cartContext1->expects($this->never())->method('getCart');
        $this->cartContext2->expects($this->once())->method('getCart')->willReturn($this->cart);

        $this->context->addContext($this->cartContext1, -1);
        $this->context->addContext($this->cartContext2, 0);

        $this->assertSame($this->cart, $this->context->getCart());
    }
}
