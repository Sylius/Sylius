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

namespace Sylius\Bundle\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class OrderPaymentRequestEligibility extends Constraint
{
    public string $message = 'sylius.payment_request.invalid_order_checkout_state';

    public function validatedBy(): string
    {
        return 'sylius_api_order_payment_request_eligibility';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
