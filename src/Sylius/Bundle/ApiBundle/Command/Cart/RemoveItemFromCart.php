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

use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;

class RemoveItemFromCart implements OrderTokenValueAwareInterface
{
    public function __construct(public ?string $orderTokenValue, public string $itemId)
    {
    }

    public static function removeFromData(string $tokenValue, string $orderItemId): self
    {
        return new self($tokenValue, $orderItemId);
    }

    public function getOrderTokenValue(): ?string
    {
        return $this->orderTokenValue;
    }

    public function setOrderTokenValue(?string $orderTokenValue): void
    {
        $this->orderTokenValue = $orderTokenValue;
    }
}
