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
use Sylius\Bundle\PaymentBundle\Command\AddPaymentRequest as BaseAddPaymentRequest;

/** @experimental */
class AddPaymentRequest extends BaseAddPaymentRequest implements IriToIdentifierConversionAwareInterface
{
}
