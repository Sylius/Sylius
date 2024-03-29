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

namespace Sylius\Bundle\PromotionBundle\Validator;

use Sylius\Bundle\PromotionBundle\Validator\Constraints\PromotionDateRange;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class PromotionDateRangeValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (null === $value) {
            return;
        }

        /** @var PromotionInterface $value */
        Assert::isInstanceOf($value, PromotionInterface::class);

        /** @var PromotionDateRange $constraint */
        Assert::isInstanceOf($constraint, PromotionDateRange::class);

        if (null === $value->getStartsAt() || null === $value->getEndsAt()) {
            return;
        }

        if ($value->getStartsAt()->getTimestamp() > $value->getEndsAt()->getTimestamp()) {
            $this->context
                ->buildViolation($constraint->message)
                ->atPath('endsAt')
                ->addViolation()
            ;
        }
    }
}
