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

use Sylius\Bundle\ApiBundle\Command\CustomerEmailAwareInterface;
use Sylius\Bundle\ApiBundle\Command\IriToIdentifierConversionAwareInterface;
use Sylius\Bundle\ApiBundle\Command\PaymentMethodCodeAwareInterface;

/** @experimental */
class AddPaymentRequest implements IriToIdentifierConversionAwareInterface
{
    public function __construct(
        public string $type,
        public mixed $requestPayload,
        public ?string $paymentId,
        public ?string $paymentMethodCode,
    ) {
    }
}
