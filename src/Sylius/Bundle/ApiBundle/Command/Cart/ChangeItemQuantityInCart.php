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
use Sylius\Bundle\ApiBundle\Command\SubresourceIdAwareInterface;

class ChangeItemQuantityInCart implements OrderTokenValueAwareInterface, SubresourceIdAwareInterface
{
    /** @var string|null */
    public $orderTokenValue;

    /** @var string|null */
    public $orderItemId;

    public function __construct(public int $quantity)
    {
    }

    public static function createFromData(string $tokenValue, string $orderItemId, int $quantity): self
    {
        $command = new self($quantity);

        $command->orderTokenValue = $tokenValue;
        $command->orderItemId = $orderItemId;

        return $command;
    }

    public function getOrderTokenValue(): ?string
    {
        return $this->orderTokenValue;
    }

    public function setOrderTokenValue(?string $orderTokenValue): void
    {
        $this->orderTokenValue = $orderTokenValue;
    }

    public function getSubresourceId(): ?string
    {
        return $this->orderItemId;
    }

    public function setSubresourceId(?string $subresourceId): void
    {
        $this->orderItemId = $subresourceId;
    }

    public function getSubresourceIdAttributeKey(): string
    {
        return 'orderItemId';
    }
}
