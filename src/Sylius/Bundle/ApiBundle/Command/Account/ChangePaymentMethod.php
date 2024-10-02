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

use Sylius\Bundle\ApiBundle\Attribute\OrderTokenValueAware;
use Sylius\Bundle\ApiBundle\Attribute\PaymentIdAware;
use Sylius\Bundle\ApiBundle\Command\IriToIdentifierConversionAwareInterface;

#[OrderTokenValueAware]
#[PaymentIdAware]
readonly class ChangePaymentMethod implements IriToIdentifierConversionAwareInterface
{
    public function __construct(
        public string $orderTokenValue,
        public mixed $paymentId,
        public string $paymentMethodCode,
    ) {
    }
}
