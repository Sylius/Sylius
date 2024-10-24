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

final class AdminResetPasswordTokenNonExpired extends Constraint
{
    public string $message = 'sylius.admin.expired_password_reset_token';

    public function validatedBy(): string
    {
        return 'sylius_api_validator_admin_non_expired_password_reset_token';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
