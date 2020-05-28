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

/**
 * @psalm-immutable
 */
class AddItemToCart
{
    /** @var string */
    public $tokenValue;

    /** @var string */
    public $productCode;

    public static function createFromData(string $tokenValue, string $productCode): self
    {
        $command = new self();

        $command->tokenValue = $tokenValue;
        $command->productCode = $productCode;

        return $command;
    }
}
