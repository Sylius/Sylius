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

use Sylius\Bundle\ApiBundle\Attribute\OrderItemIdAware;
use Sylius\Bundle\ApiBundle\Attribute\OrderTokenValueAware;

#[OrderTokenValueAware]
#[OrderItemIdAware]
readonly class ChangeItemQuantityInCart
{
    public function __construct(
        public string $orderTokenValue,
        public mixed $orderItemId,
        public int $quantity,
    ) {
    }
}
