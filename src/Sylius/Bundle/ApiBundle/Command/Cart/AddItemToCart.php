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
class AddItemToCart implements OrderTokenValueAwareInterface
{
    /** @var string|null */
    public $orderTokenValue;

    /**
     * @psalm-immutable
     *
     * @var string
     */
    public $productVariantCode;

    /**
     * @psalm-immutable
     *
     * @var int
     */
    public $quantity;

    public function __construct(string $productVariantCode, int $quantity)
    {
        $this->productVariantCode = $productVariantCode;
        $this->quantity = $quantity;
    }

    public static function createFromData(string $tokenValue, string $productVariantCode, int $quantity): self
    {
        $command = new self($productVariantCode, $quantity);

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
