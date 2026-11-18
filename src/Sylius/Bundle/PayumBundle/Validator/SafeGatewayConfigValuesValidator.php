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

namespace Sylius\Bundle\PayumBundle\Validator;

use Sylius\Bundle\PayumBundle\Validator\Constraints\SafeGatewayConfigValues;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class SafeGatewayConfigValuesValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof SafeGatewayConfigValues) {
            throw new UnexpectedTypeException($constraint, SafeGatewayConfigValues::class);
        }

        if (!is_array($value)) {
            return;
        }

        foreach ($value as $configValue) {
            if (
                (is_string($configValue) && $this->isInjectable($configValue)) ||
                (is_array($configValue) && is_callable($configValue))
            ) {
                $this->context->buildViolation($constraint->forbiddenValueMessage)->addViolation();
            }
        }
    }

    private function isInjectable(string $value): bool
    {
        if ('' === $value) {
            return false;
        }

        if (1 === preg_match('/^%.*%$/', $value)) {
            return true;
        }

        if (str_starts_with($value, '@')) {
            return true;
        }

        return is_callable($value);
    }
}
