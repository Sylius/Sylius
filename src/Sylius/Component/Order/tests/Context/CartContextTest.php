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
use Sylius\Component\Order\Context\CartContext;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Resource\Factory\FactoryInterface;

final class CartContextTest extends TestCase
{
    /** @var FactoryInterface<CartContext>&MockObject */
    private FactoryInterface&MockObject $cartFactory;

    private CartContext $cartContext;

    private MockObject&OrderInterface $cart;

    protected function setUp(): void
    {
        $this->cartFactory = $this->createMock(FactoryInterface::class);
        $this->cartContext = new CartContext($this->cartFactory);
        $this->cart = $this->createMock(OrderInterface::class);
    }

    public function testImplementsCartContextInterface(): void
    {
        $this->assertInstanceOf(CartContextInterface::class, $this->cartContext);
    }

    public function testAlwaysReturnsANewCart(): void
    {
        $this->cartFactory->expects($this->once())->method('createNew')->willReturn($this->cart);

        $this->assertSame($this->cart, $this->cartContext->getCart());
    }
}
