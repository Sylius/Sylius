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

namespace Sylius\Component\Order\Modifier;

use Sylius\Component\Order\Model\OrderItemInterface;

interface OrderItemQuantityModifierInterface
{
    /**
     * @param OrderItemInterface $orderItem
     * @param int $targetQuantity
     */
    public function modify(OrderItemInterface $orderItem, int $targetQuantity): void;
}
