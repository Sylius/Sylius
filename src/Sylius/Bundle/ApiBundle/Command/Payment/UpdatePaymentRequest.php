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
use Sylius\Bundle\ApiBundle\Command\PaymentRequestHashAwareInterface;

/** @experimental */
class UpdatePaymentRequest implements PaymentRequestHashAwareInterface, IriToIdentifierConversionAwareInterface
{
    protected ?string $hash = null;

    public function __construct(
        private mixed $requestPayload = null,
    ) {
    }

    public function getRequestPayload(): mixed
    {
        return $this->requestPayload;
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
