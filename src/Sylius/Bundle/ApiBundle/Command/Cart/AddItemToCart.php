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

class AddItemToCart
{
    /** @var string|null */
    public $tokenValue;

    /**
     * @var string
     * @psalm-immutable
     */
    public $productCode;

    /**
     * @var int
     * @psalm-immutable
     */
    public $quantity;

    public function __construct(string $productCode, int $quantity)
    {
        $this->productCode = $productCode;
        $this->quantity = $quantity;
    }

    public static function createFromData(string $tokenValue, string $productCode, int $quantity): self
    {
        $command = new self($productCode, $quantity);

        $command->tokenValue = $tokenValue;

        return $command;
    }
}
