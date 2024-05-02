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

use Sylius\Bundle\ApiBundle\Command\IriToIdentifierConversionAwareInterface;
use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;

class AddItemToCart implements OrderTokenValueAwareInterface, IriToIdentifierConversionAwareInterface
{
    /** @var string|null */
    public $orderTokenValue;

    public function __construct(public string $productVariantCode, public int $quantity)
    {
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
