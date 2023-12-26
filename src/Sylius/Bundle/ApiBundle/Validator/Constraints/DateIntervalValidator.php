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
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/** @experimental */
final class DateIntervalValidator extends ConstraintValidator
{
    /**
     * @param string $value
     * @param DateInterval $constraint
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        Assert::string($value);
        Assert::isInstanceOf($constraint, DateInterval::class);

        try {
            new \DateInterval($value);
        } catch (\Exception) {
            $this->context->addViolation($constraint->message);
        }
    }
}
