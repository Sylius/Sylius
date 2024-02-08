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

namespace spec\Sylius\Bundle\ApiBundle\CommandHandler\Cart;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Cart\ChangeItemQuantityInCart;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Order\Repository\OrderItemRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ChangeItemQuantityInCartHandlerSpec extends ObjectBehavior
{
    function let(
        OrderItemRepositoryInterface $orderItemRepository,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        OrderProcessorInterface $orderProcessor,
    ) {
        $this->beConstructedWith($orderItemRepository, $orderItemQuantityModifier, $orderProcessor);
    }

    function it_is_a_message_handler(): void
    {
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    function it_changes_order_item_quantity(
        OrderItemRepositoryInterface $orderItemRepository,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        OrderProcessorInterface $orderProcessor,
        OrderInterface $cart,
        OrderItemInterface $cartItem,
    ): void {
        $orderItemRepository->findOneByIdAndCartTokenValue(
            'ORDER_ITEM_ID',
            'TOKEN_VALUE',
        )->willReturn($cartItem);

        $cartItem->getOrder()->willReturn($cart);

        $cart->getTokenValue()->willReturn('TOKEN_VALUE');

        $orderItemQuantityModifier->modify($cartItem, 5)->shouldBeCalled();
        $orderProcessor->process($cart)->shouldBeCalled();

        $this(ChangeItemQuantityInCart::createFromData('TOKEN_VALUE', 'ORDER_ITEM_ID', 5));
    }
}
