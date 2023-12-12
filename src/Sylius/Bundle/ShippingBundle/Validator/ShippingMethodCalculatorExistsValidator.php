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

namespace Sylius\Bundle\ShippingBundle\Validator;

use Sylius\Bundle\ShippingBundle\Validator\Constraint\ShippingMethodCalculatorExists;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class ShippingMethodCalculatorExistsValidator extends ConstraintValidator
{
    /** @param array<string, string> $calculators */
    public function __construct(private array $calculators)
    {
    }

    /** @param string|null $value */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ShippingMethodCalculatorExists) {
            throw new UnexpectedTypeException($constraint, ShippingMethodCalculatorExists::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        if (!in_array($value, array_keys($this->calculators), true)) {
            $this->context->buildViolation($constraint->invalidShippingCalculator)
                ->setParameter('{{ available_calculators }}', implode(', ', array_keys($this->calculators)))
                ->addViolation()
            ;
        }
    }
}
