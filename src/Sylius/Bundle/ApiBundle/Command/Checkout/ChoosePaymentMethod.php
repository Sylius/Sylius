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

use Sylius\Bundle\ApiBundle\Command\IriToIdentifierConversionAwareInterface;
use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;
use Sylius\Bundle\ApiBundle\Command\PaymentIdAwareInterface;
use Sylius\Bundle\ApiBundle\Command\PaymentMethodCodeAwareInterface;

class ChoosePaymentMethod implements OrderTokenValueAwareInterface, PaymentIdAwareInterface, PaymentMethodCodeAwareInterface, IriToIdentifierConversionAwareInterface
{
    public function __construct(
        protected string $paymentMethodCode,
        protected mixed $paymentId,
        protected string $orderTokenValue,
    ) {
    }

    public function getPaymentMethodCode(): ?string
    {
        return $this->paymentMethodCode;
    }

    public function getPaymentId(): mixed
    {
        return $this->paymentId;
    }

    public function getOrderTokenValue(): ?string
    {
        return $this->orderTokenValue;
    }
}
