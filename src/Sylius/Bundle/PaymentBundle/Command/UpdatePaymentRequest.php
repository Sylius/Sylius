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

namespace Sylius\Bundle\PaymentBundle\Command;

/** @experimental */
class UpdatePaymentRequest
{
    protected ?string $hash = null;

    public function __construct(
        private mixed $payload = null,
    ) {
    }

    public function getPayload(): mixed
    {
        return $this->payload;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(?string $hash): void
    {
        $this->hash = $hash;
    }
}
