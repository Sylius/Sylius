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

namespace Sylius\Bundle\ApiBundle\Command\Cart;

use Sylius\Bundle\ApiBundle\Command\OrderItemIdAwareInterface;
use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;

class ChangeItemQuantityInCart implements OrderTokenValueAwareInterface, OrderItemIdAwareInterface
{
    public function __construct(
        public int $quantity,
        public ?int $orderItemId = null,
        public ?string $orderTokenValue = null,
    ) {
    }

    public function getOrderTokenValue(): ?string
    {
        return $this->orderTokenValue;
    }

    public function getOrderItemId(): ?int
    {
        return $this->orderItemId;
    }
}
