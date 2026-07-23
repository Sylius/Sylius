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

use Sylius\Bundle\ApiBundle\Attribute\OrderTokenValueAware;
use Sylius\Bundle\ApiBundle\Attribute\PaymentRequestActionAware;
use Sylius\Bundle\ApiBundle\Command\IriToIdentifierConversionAwareInterface;

/** @experimental */
#[OrderTokenValueAware]
#[PaymentRequestActionAware]
class AddPaymentRequest implements IriToIdentifierConversionAwareInterface
{
    public function __construct(
        public readonly string $orderTokenValue,
        public readonly mixed $paymentId,
        public readonly string $paymentMethodCode,
        public readonly ?string $action = null,
        public readonly mixed $payload = null,
    ) {
    }
}
