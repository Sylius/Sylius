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

final class RemoveItemFromCart
{
    /** @var string|null */
    public $tokenValue;

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

        $command->tokenValue = $tokenValue;

        return $command;
    }
}
