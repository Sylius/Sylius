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

namespace Sylius\Bundle\ApiBundle\Command\Account;

use Sylius\Bundle\ApiBundle\Command\IriToIdentifierConversionAwareInterface;
use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;
use Sylius\Bundle\ApiBundle\Command\PaymentIdAwareInterface;

class ChangePaymentMethod implements OrderTokenValueAwareInterface, PaymentIdAwareInterface, IriToIdentifierConversionAwareInterface
{
    public function __construct(
        public string $paymentMethodCode,
        public ?int $paymentId = null,
        public ?string $orderTokenValue = null,
    ) {
    }

    public function getOrderTokenValue(): ?string
    {
        return $this->orderTokenValue;
    }

    public function getPaymentId(): ?int
    {
        return $this->paymentId;
    }
}
