<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
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
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Order\Repository\OrderItemRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class RemoveItemFromCartHandler implements MessageHandlerInterface
{
    private OrderItemRepositoryInterface $orderItemRepository;

    private OrderModifierInterface $orderModifier;

    private OrderProcessorInterface $orderProcessor;

    public function __construct(
        OrderItemRepositoryInterface $orderItemRepository,
        OrderModifierInterface $orderModifier,
        OrderProcessorInterface $orderProcessor
    ) {
        $this->orderItemRepository = $orderItemRepository;
        $this->orderModifier = $orderModifier;
        $this->orderProcessor = $orderProcessor;
    }

    public function __invoke(RemoveItemFromCart $removeItemFromCart): OrderInterface
    {
        /** @var OrderItemInterface|null $orderItem */
        $orderItem = $this->orderItemRepository->findOneByIdAndCartTokenValue(
            $removeItemFromCart->itemId,
            $removeItemFromCart->orderTokenValue
        );

        Assert::notNull($orderItem);

        /** @var OrderInterface $cart */
        $cart = $orderItem->getOrder();

        Assert::same($cart->getTokenValue(), $removeItemFromCart->orderTokenValue);

        $this->orderModifier->removeFromOrder($cart, $orderItem);

        $this->orderProcessor->process($cart);

        return $cart;
    }
}
