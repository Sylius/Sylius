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

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class MaxIntegerValidator extends ConstraintValidator
{
    public function __construct(private int $maxInt)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        Assert::isInstanceOf($constraint, MaxInteger::class);

        if ($value >= $this->maxInt) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ compared_value }}', (string) $this->maxInt)
                ->addViolation()
            ;
        }
    }
}
