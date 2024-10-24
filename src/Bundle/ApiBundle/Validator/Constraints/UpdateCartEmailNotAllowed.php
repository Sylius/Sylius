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

final class UpdateCartEmailNotAllowed extends Constraint
{
    public string $message = 'sylius.checkout.email.not_changeable';

    public function validatedBy(): string
    {
        return 'sylius_validator_update_cart_email_not_allowed';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
