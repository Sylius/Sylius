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

final class CanPaymentMethodBeChanged extends Constraint
{
    public const CANNOT_CHANGE_PAYMENT_METHOD_FOR_CANCELLED_ORDER = 'sylius.payment_method.cannot_change_payment_method_for_cancelled_order';

    public function validatedBy(): string
    {
        return 'sylius_api_validator_can_payment_method_be_changed';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
