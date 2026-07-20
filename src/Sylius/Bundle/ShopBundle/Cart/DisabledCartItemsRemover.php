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

namespace Sylius\Bundle\ShopBundle\Cart;

use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Order\SyliusCartEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class DisabledCartItemsRemover
{
    public function __construct(
        private readonly ObjectManager $manager,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function removeFromOrder(OrderInterface $order): bool
    {
        $channel = $order->getChannel();
        $itemsToRemove = [];

        foreach ($order->getItems() as $item) {
            if (!$this->isItemAvailable($item->getVariant(), $item->getProduct(), $channel)) {
                $itemsToRemove[] = $item;
            }
        }

        if ([] === $itemsToRemove) {
            return false;
        }

        foreach ($itemsToRemove as $item) {
            $order->removeItem($item);
        }

        $this->eventDispatcher->dispatch(new GenericEvent($order), SyliusCartEvents::CART_CHANGE);
        $this->manager->flush();
        $this->manager->refresh($order);

        return true;
    }

    private function isItemAvailable(
        ?ProductVariantInterface $variant,
        ?ProductInterface $product,
        ?ChannelInterface $channel,
    ): bool {
        if (null === $variant || !$variant->isEnabled()) {
            return false;
        }

        if (null === $product || !$product->isEnabled()) {
            return false;
        }

        return null === $channel || $product->hasChannel($channel);
    }
}
