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

/** @experimental */
final class EndDateIsNotBeforeStartDate extends Constraint
{
    public string $message = 'sylius.date_period.end_date_is_not_before_start_date';

    public function validatedBy(): string
    {
        return 'sylius_api_validator_date_period_end_date_is_not_before_start_date';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
