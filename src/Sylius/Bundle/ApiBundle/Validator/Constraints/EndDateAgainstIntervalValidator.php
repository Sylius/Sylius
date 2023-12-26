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
final class EndDateAgainstIntervalValidator extends ConstraintValidator
{
    /**
     * @param \DatePeriod $value
     * @param EndDateAgainstInterval $constraint
     *
     * Validates that the end date is a multiple of the interval.
     * The end date is adjusted by subtracting one second to make it inclusive (closed interval).
     * If the adjusted end date does not match the provided end date, an exception is thrown.
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, \DatePeriod::class);
        Assert::isInstanceOf($constraint, EndDateAgainstInterval::class);

        $currentDate = clone $value->getStartDate();
        $endDate = $value->getEndDate();
        $interval = $value->getDateInterval();

        while ($currentDate <= $endDate) {
            $currentDate = $currentDate->add($interval);
        }

        /** We shift to make closed interval. */
        $intervalEndDate = $currentDate->modify('-1 second');

        if ($intervalEndDate != $endDate) {
            $this
                ->context
                ->buildViolation($constraint->message)
                ->setParameter('%givenDate%', $endDate->format('Y-m-d H:i:s'))
                ->setParameter('%expectedDate%', $intervalEndDate->format('Y-m-d H:i:s'))
                ->addViolation()
            ;
        }
    }
}
