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

namespace Sylius\Bundle\ApiBundle\Validator\Constraints;

use Sylius\Bundle\ApiBundle\Command\ResetPassword;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/** @experimental */
final class ConfirmResetPasswordValidator extends ConstraintValidator
{
    /** @param ResetPassword|mixed $value */
    public function validate($value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, ResetPassword::class);

        /** @var ConfirmResetPassword $constraint */
        Assert::isInstanceOf($constraint, ConfirmResetPassword::class);

        if ($value->confirmNewPassword !== $value->newPassword) {
            $this->context->buildViolation($constraint->message)
                ->atPath('newPassword')
                ->addViolation()
            ;
        }
    }
}
