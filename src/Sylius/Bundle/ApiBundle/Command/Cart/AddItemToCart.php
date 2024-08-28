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
    public function __construct(
        protected string $productVariantCode,
        protected int $quantity,
        protected string $orderTokenValue,
    ) {
    }

    public function getProductVariantCode(): ?string
    {
        return $this->productVariantCode;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getOrderTokenValue(): ?string
    {
        return $this->orderTokenValue;
    }
}
