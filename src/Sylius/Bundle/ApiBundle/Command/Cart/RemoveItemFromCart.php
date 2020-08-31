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

namespace Sylius\Bundle\ApiBundle\Command\Cart;

use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;

/** @experimental */
class RemoveItemFromCart implements OrderTokenValueAwareInterface
{
    /** @var string|null */
    public $orderTokenValue;

    /**
     * @var string
     * @psalm-immutable
     */
    public $orderItemId;

    public function __construct(string $orderItemId)
    {
        $this->orderItemId = $orderItemId;
    }

    public static function removeFromData(string $tokenValue, string $orderItemId): self
    {
        $command = new self($orderItemId);

        $command->orderTokenValue = $tokenValue;

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
}
