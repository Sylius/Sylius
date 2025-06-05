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
use spec\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;
use Sylius\Bundle\ApiBundle\Command\Cart\ChangeItemQuantityInCart;
use Sylius\Bundle\ApiBundle\CommandHandler\Cart\ChangeItemQuantityInCartHandler;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Order\Repository\OrderItemRepositoryInterface;

final class ChangeItemQuantityInCartHandlerTest extends TestCase
{
    /** @var OrderItemRepositoryInterface|MockObject */
    private MockObject $orderItemRepositoryMock;

    /** @var OrderItemQuantityModifierInterface|MockObject */
    private MockObject $orderItemQuantityModifierMock;

    /** @var OrderProcessorInterface|MockObject */
    private MockObject $orderProcessorMock;

    private ChangeItemQuantityInCartHandler $changeItemQuantityInCartHandler;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        $this->orderItemRepositoryMock = $this->createMock(OrderItemRepositoryInterface::class);
        $this->orderItemQuantityModifierMock = $this->createMock(OrderItemQuantityModifierInterface::class);
        $this->orderProcessorMock = $this->createMock(OrderProcessorInterface::class);
        $this->changeItemQuantityInCartHandler = new ChangeItemQuantityInCartHandler($this->orderItemRepositoryMock, $this->orderItemQuantityModifierMock, $this->orderProcessorMock);
    }

    public function testChangesOrderItemQuantity(): void
    {
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        /** @var OrderItemInterface|MockObject $cartItemMock */
        $cartItemMock = $this->createMock(OrderItemInterface::class);
        $this->orderItemRepositoryMock->expects(self::once())->method('findOneByIdAndCartTokenValue')->with(123, 'TOKEN_VALUE')->willReturn($cartItemMock);
        $cartItemMock->expects(self::once())->method('getOrder')->willReturn($cartMock);
        $cartMock->expects(self::once())->method('getTokenValue')->willReturn('TOKEN_VALUE');
        $this->orderItemQuantityModifierMock->expects(self::once())->method('modify')->with($cartItemMock, 5);
        $this->orderProcessorMock->expects(self::once())->method('process')->with($cartMock);
        $this(new ChangeItemQuantityInCart(orderTokenValue: 'TOKEN_VALUE', orderItemId: 123, quantity: 5));
    }
}
