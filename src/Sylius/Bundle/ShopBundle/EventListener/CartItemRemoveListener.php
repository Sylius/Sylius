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

namespace Sylius\Bundle\ShopBundle\EventListener;

use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final readonly class CartItemRemoveListener
{
    public function __construct(private OrderModifierInterface $orderModifier)
    {
    }

    public function removeFromOrder(GenericEvent $event): void
    {
        $orderItem = $event->getSubject();

        Assert::isInstanceOf($orderItem, OrderItemInterface::class);

        $this->orderModifier->removeFromOrder($orderItem->getOrder(), $orderItem);
    }
}
