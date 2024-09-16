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

namespace Sylius\Bundle\ApiBundle\Command\Payment;

use Sylius\Bundle\ApiBundle\Command\IriToIdentifierConversionAwareInterface;

/** @experimental */
class AddPaymentRequest implements IriToIdentifierConversionAwareInterface
{
    public function __construct(
        private int|string $paymentId,
        private string $paymentMethodCode,
        private string $action,
        private mixed $payload = null,
    ) {
    }

    public function getPaymentId(): int|string
    {
        return $this->paymentId;
    }

    public function getPaymentMethodCode(): string
    {
        return $this->paymentMethodCode;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getPayload(): mixed
    {
        return $this->payload;
    }
}
