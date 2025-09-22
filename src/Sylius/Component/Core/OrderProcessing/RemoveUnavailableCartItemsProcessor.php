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

namespace Sylius\Component\Core\OrderProcessing;

use Sylius\Component\Core\Event\CartItemsRemovedEvent;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface as CoreOrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\OrderProcessing\Checker\ProductVariantChannelEligibilityCheckerInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class RemoveUnavailableCartItemsProcessor implements OrderProcessorInterface
{
    public function __construct(
        private readonly ProductVariantChannelEligibilityCheckerInterface $eligibilityChecker,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function process(BaseOrderInterface $order): void
    {
        if (!$order instanceof CoreOrderInterface) {
            return;
        }

        $channel = $order->getChannel();
        if (!$channel instanceof ChannelInterface) {
            return;
        }

        if ($order->isEmpty()) {
            return;
        }

        $itemsToRemove = [];
        $removedItemNames = [];

        /** @var OrderItemInterface $item */
        foreach ($order->getItems() as $item) {
            $variant = $item->getVariant();
            if (!$variant instanceof ProductVariantInterface) {
                $itemsToRemove[] = $item;
                $removedItemNames[] = $item->getProductName();

                continue;
            }

            if (!$this->eligibilityChecker->isEligible($variant, $channel)) {
                $itemsToRemove[] = $item;
                $removedItemNames[] = $item->getProductName();
            }
        }

        foreach ($itemsToRemove as $item) {
            $order->removeItem($item);
        }

        if ($itemsToRemove !== []) {
            $this->eventDispatcher->dispatch(new CartItemsRemovedEvent(
                orderToken: method_exists($order, 'getTokenValue') ? (string) $order->getTokenValue() : null,
                channelCode: (string) $channel->getCode(),
                removedItemNames: $removedItemNames,
                removedCount: count($itemsToRemove),
            ));
        }
    }
}
