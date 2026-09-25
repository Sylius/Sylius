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

use Sylius\Component\Core\Model\AdminUserInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class AtLeastOneAccessLevelValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof AtLeastOneAccessLevel) {
            throw new UnexpectedTypeException($constraint, AtLeastOneAccessLevel::class);
        }

        if (null === $value) {
            return;
        }

        if (!$value instanceof AdminUserInterface) {
            throw new UnexpectedValueException($value, AdminUserInterface::class);
        }

        if ($value->hasAdministrationAccess() || $value->hasApiAccess()) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->atPath('administrationAccess')
            ->addViolation()
        ;
    }
}
