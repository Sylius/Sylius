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

use Symfony\Component\Validator\Constraint;

/** @experimental */
final class ResetPasswordTokenExists extends Constraint
{
    public string $message = 'sylius.reset_password.invalid_token';

    public function validatedBy(): string
    {
        return 'sylius_api_reset_password_token_exists';
    }
}
