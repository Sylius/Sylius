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

final class ConfirmResetPassword extends Constraint
{
    /** @var string */
    public $message = 'sylius.user.plainPassword.mismatch';

    public function validatedBy(): string
    {
        return 'sylius_api_confirm_reset_password';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
