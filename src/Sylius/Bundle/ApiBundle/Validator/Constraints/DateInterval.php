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
final class DateInterval extends Constraint
{
    public string $message = 'sylius.date_interval.invalid';

    public function validatedBy(): string
    {
        return 'sylius_api_validator_date_interval';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
