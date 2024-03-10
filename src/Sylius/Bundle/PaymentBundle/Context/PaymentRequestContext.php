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

namespace Sylius\Bundle\PaymentBundle\Context;

use Sylius\Component\Payment\Model\PaymentRequestInterface;

final class PaymentRequestContext implements PaymentRequestContextInterface
{
    private ?PaymentRequestInterface $paymentRequest = null;

    public function isEnabled(): bool
    {
        return null !== $this->paymentRequest;
    }

    public function enable(PaymentRequestInterface $paymentRequest): void
    {
        $this->paymentRequest = $paymentRequest;
    }

    public function getPaymentRequest(): ?PaymentRequestInterface
    {
        return $this->paymentRequest;
    }

    public function disable(): void
    {
        $this->paymentRequest = null;
    }
}
