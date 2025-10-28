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
use Sylius\Bundle\ApiBundle\Command\Cart\ChangeItemQuantityInCart;
use Sylius\Bundle\ApiBundle\CommandHandler\Cart\ChangeItemQuantityInCartHandler;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Order\Repository\OrderItemRepositoryInterface;
use Tests\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;

final class ChangeItemQuantityInCartHandlerTest extends TestCase
{
    private MockObject&OrderItemRepositoryInterface $orderItemRepository;

    private MockObject&OrderItemQuantityModifierInterface $orderItemQuantityModifier;

    private MockObject&OrderProcessorInterface $orderProcessor;

    private ChangeItemQuantityInCartHandler $handler;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderItemRepository = $this->createMock(OrderItemRepositoryInterface::class);
        $this->orderItemQuantityModifier = $this->createMock(OrderItemQuantityModifierInterface::class);
        $this->orderProcessor = $this->createMock(OrderProcessorInterface::class);
        $this->handler = new ChangeItemQuantityInCartHandler(
            $this->orderItemRepository,
            $this->orderItemQuantityModifier,
            $this->orderProcessor,
        );
    }

    public function testChangesOrderItemQuantity(): void
    {
        /** @var OrderInterface|MockObject $cart */
        $cart = $this->createMock(OrderInterface::class);
        /** @var OrderItemInterface|MockObject $cartItem */
        $cartItem = $this->createMock(OrderItemInterface::class);
        $this->orderItemRepository->expects(self::once())
            ->method('findOneByIdAndCartTokenValue')
            ->with(123, 'TOKEN_VALUE')
            ->willReturn($cartItem);
        $cartItem->expects(self::once())->method('getOrder')->willReturn($cart);
        $cart->expects(self::once())->method('getTokenValue')->willReturn('TOKEN_VALUE');
        $this->orderItemQuantityModifier->expects(self::once())->method('modify')->with($cartItem, 5);
        $this->orderProcessor->expects(self::once())->method('process')->with($cart);
        $this->handler->__invoke(new ChangeItemQuantityInCart(orderTokenValue: 'TOKEN_VALUE', orderItemId: 123, quantity: 5));
    }
}
