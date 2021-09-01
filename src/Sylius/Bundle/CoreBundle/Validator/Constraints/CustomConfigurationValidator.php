<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CustomConfigurationValidator extends ConstraintValidator
{
    private ConstraintResolver $constraintResolver;
    private ValidatorInterface $validator;

    public function __construct(ConstraintResolver $constraintResolver, ValidatorInterface $validator)
    {
        $this->constraintResolver = $constraintResolver;
        $this->validator = $validator;
    }

    public function validate($value, Constraint $constraint)
    {
        $configurationConstraint = $this->constraintResolver->resolveForType('custom-configuration');
        $this->validator->validate($value, $configurationConstraint);
    }
}
