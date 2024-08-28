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

namespace Sylius\Bundle\ApiBundle\Command\Checkout;

use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;

class CompleteOrder implements OrderTokenValueAwareInterface
{
    public function __construct(
        protected ?string $notes = null,
        protected ?string $orderTokenValue = null,
    ) {
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function getOrderTokenValue(): ?string
    {
        return $this->orderTokenValue;
    }
}
