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

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use spec\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;
use Sylius\Bundle\ApiBundle\Command\Cart\RemoveItemFromCart;
use Sylius\Bundle\ApiBundle\CommandHandler\Cart\RemoveItemFromCartHandler;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Sylius\Component\Order\Repository\OrderItemRepositoryInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;

final class RemoveItemFromCartHandlerTest extends TestCase
{
    /** @var OrderItemRepositoryInterface|MockObject */
    private MockObject $orderItemRepositoryMock;

    /** @var OrderModifierInterface|MockObject */
    private MockObject $orderModifierMock;

    /** @var ProductVariantResolverInterface|MockObject */
    private MockObject $variantResolverMock;

    private RemoveItemFromCartHandler $removeItemFromCartHandler;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        $this->orderItemRepositoryMock = $this->createMock(OrderItemRepositoryInterface::class);
        $this->orderModifierMock = $this->createMock(OrderModifierInterface::class);
        $this->variantResolverMock = $this->createMock(ProductVariantResolverInterface::class);
        $this->removeItemFromCartHandler = new RemoveItemFromCartHandler($this->orderItemRepositoryMock, $this->orderModifierMock, $this->variantResolverMock);
    }

    public function testRemovesOrderItemFromCart(): void
    {
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        /** @var OrderItemInterface|MockObject $cartItemMock */
        $cartItemMock = $this->createMock(OrderItemInterface::class);
        $this->orderItemRepositoryMock->expects(self::once())->method('findOneByIdAndCartTokenValue')->with('ORDER_ITEM_ID', 'TOKEN_VALUE')->willReturn($cartItemMock);
        $cartItemMock->expects(self::once())->method('getOrder')->willReturn($cartMock);
        $cartMock->expects(self::once())->method('getTokenValue')->willReturn('TOKEN_VALUE');
        $this->orderModifierMock->expects(self::once())->method('removeFromOrder')->with($cartMock, $cartItemMock);
        self::assertSame($cartMock, $this(new RemoveItemFromCart(orderTokenValue: 'TOKEN_VALUE', itemId: 'ORDER_ITEM_ID')));
    }

    public function testThrowsAnExceptionIfOrderItemWasNotFound(): void
    {
        /** @var OrderItemInterface|MockObject $cartItemMock */
        $cartItemMock = $this->createMock(OrderItemInterface::class);
        $this->orderItemRepositoryMock->expects(self::once())->method('findOneByIdAndCartTokenValue')->with('ORDER_ITEM_ID', 'TOKEN_VALUE')->willReturn(null);
        $cartItemMock->expects(self::never())->method('getOrder');
        $this->expectException(InvalidArgumentException::class);
        $this->removeItemFromCartHandler->__invoke(new RemoveItemFromCart(orderTokenValue: 'TOKEN_VALUE', itemId: 'ORDER_ITEM_ID'));
    }

    public function testThrowsAnExceptionIfCartTokenValueWasNotProperly(): void
    {
        /** @var OrderItemInterface|MockObject $cartItemMock */
        $cartItemMock = $this->createMock(OrderItemInterface::class);
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        $this->orderItemRepositoryMock->expects(self::once())->method('findOneByIdAndCartTokenValue')->with('ORDER_ITEM_ID', 'TOKEN_VALUE')->willReturn($cartItemMock);
        $cartItemMock->expects(self::once())->method('getOrder')->willReturn($cartMock);
        $cartMock->expects(self::once())->method('getTokenValue')->willReturn('WRONG_TOKEN_VALUE_');
        $this->orderModifierMock->expects(self::never())->method('removeFromOrder')->with(null, $cartItemMock);
        $this->expectException(InvalidArgumentException::class);
        $this->removeItemFromCartHandler->__invoke(new RemoveItemFromCart(orderTokenValue: 'TOKEN_VALUE', itemId: 'ORDER_ITEM_ID'));
    }
}
