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

namespace Sylius\Component\Core\Cart\Modifier;

use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;

final class LimitingOrderItemQuantityModifier implements OrderItemQuantityModifierInterface
{
    public function __construct(private OrderItemQuantityModifierInterface $decoratedOrderItemQuantityModifier, private int $limit)
    {
    }

    public function modify(OrderItemInterface $orderItem, int $targetQuantity): void
    {
        $targetQuantity = min($targetQuantity, $this->limit);

        $this->decoratedOrderItemQuantityModifier->modify($orderItem, $targetQuantity);
    }
}
