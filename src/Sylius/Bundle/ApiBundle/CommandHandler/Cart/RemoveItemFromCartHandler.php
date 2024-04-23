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

namespace Sylius\Bundle\ApiBundle\CommandHandler\Cart;

use Sylius\Bundle\ApiBundle\Command\Cart\RemoveItemFromCart;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Sylius\Component\Order\Repository\OrderItemRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

final class RemoveItemFromCartHandler implements MessageHandlerInterface
{
    public function __construct(
        private OrderItemRepositoryInterface $orderItemRepository,
        private OrderModifierInterface $orderModifier,
    ) {
    }

    public function __invoke(RemoveItemFromCart $removeItemFromCart): OrderInterface
    {
        /** @var OrderItemInterface|null $orderItem */
        $orderItem = $this->orderItemRepository->findOneByIdAndCartTokenValue(
            $removeItemFromCart->itemId,
            $removeItemFromCart->orderTokenValue,
        );

        Assert::notNull($orderItem);

        /** @var OrderInterface $cart */
        $cart = $orderItem->getOrder();

        Assert::same($cart->getTokenValue(), $removeItemFromCart->orderTokenValue);

        $this->orderModifier->removeFromOrder($cart, $orderItem);

        return $cart;
    }
}
