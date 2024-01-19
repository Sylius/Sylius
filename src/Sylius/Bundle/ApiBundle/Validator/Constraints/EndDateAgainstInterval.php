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
final class EndDateAgainstInterval extends Constraint
{
    /** @var string */
    public $message = 'sylius.date_period.end_date_must_be_multiple_of_interval';

    public function validatedBy(): string
    {
        return 'sylius_api_validator_end_date_against_interval';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
