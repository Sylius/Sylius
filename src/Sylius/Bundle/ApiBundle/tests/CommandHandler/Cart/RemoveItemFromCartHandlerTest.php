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

namespace Tests\Sylius\Bundle\ApiBundle\CommandHandler\Cart;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Command\Cart\RemoveItemFromCart;
use Sylius\Bundle\ApiBundle\CommandHandler\Cart\RemoveItemFromCartHandler;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Sylius\Component\Order\Repository\OrderItemRepositoryInterface;
use Tests\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;

final class RemoveItemFromCartHandlerTest extends TestCase
{
    private MockObject&OrderItemRepositoryInterface $orderItemRepository;

    private MockObject&OrderModifierInterface $orderModifier;

    private RemoveItemFromCartHandler $handler;

    private OrderInterface $cart;

    private OrderItemInterface $cartItem;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderItemRepository = $this->createMock(OrderItemRepositoryInterface::class);
        $this->orderModifier = $this->createMock(OrderModifierInterface::class);
        $this->handler = new RemoveItemFromCartHandler($this->orderItemRepository, $this->orderModifier);
        $this->cart = $this->createMock(OrderInterface::class);
        $this->cartItem = $this->createMock(OrderItemInterface::class);
    }

    public function testRemovesOrderItemFromCart(): void
    {
        $this->orderItemRepository->expects(self::once())
            ->method('findOneByIdAndCartTokenValue')
            ->with('ORDER_ITEM_ID', 'TOKEN_VALUE')
            ->willReturn($this->cartItem);
        $this->cartItem->expects(self::once())->method('getOrder')->willReturn($this->cart);
        $this->cart->expects(self::once())->method('getTokenValue')->willReturn('TOKEN_VALUE');
        $this->orderModifier->expects(self::once())->method('removeFromOrder')->with($this->cart, $this->cartItem);
        self::assertSame($this->cart, $this->handler->__invoke(
            new RemoveItemFromCart(orderTokenValue: 'TOKEN_VALUE', itemId: 'ORDER_ITEM_ID'),
        ));
    }

    public function testThrowsAnExceptionIfOrderItemWasNotFound(): void
    {
        $this->orderItemRepository->expects(self::once())
            ->method('findOneByIdAndCartTokenValue')
            ->with('ORDER_ITEM_ID', 'TOKEN_VALUE')->willReturn(null);
        $this->cartItem->expects(self::never())->method('getOrder');
        self::expectException(\InvalidArgumentException::class);
        $this->handler->__invoke(new RemoveItemFromCart(orderTokenValue: 'TOKEN_VALUE', itemId: 'ORDER_ITEM_ID'));
    }

    public function testThrowsAnExceptionIfCartTokenValueWasNotProperly(): void
    {
        $this->orderItemRepository->expects(self::once())
            ->method('findOneByIdAndCartTokenValue')
            ->with('ORDER_ITEM_ID', 'TOKEN_VALUE')
            ->willReturn($this->cartItem);
        $this->cartItem->expects(self::once())->method('getOrder')->willReturn($this->cart);
        $this->cart->expects(self::once())->method('getTokenValue')->willReturn('WRONG_TOKEN_VALUE_');
        $this->orderModifier->expects(self::never())
            ->method('removeFromOrder')
            ->with(null, $this->cartItem);
        self::expectException(\InvalidArgumentException::class);
        $this->handler->__invoke(new RemoveItemFromCart(orderTokenValue: 'TOKEN_VALUE', itemId: 'ORDER_ITEM_ID'));
    }
}
