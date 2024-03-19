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

final class ResendOrderConfirmationEmailWithValidOrderState extends Constraint
{
    public string $message = 'sylius.resend_order_confirmation_email.invalid_order_state';

    public function validatedBy(): string
    {
        return 'sylius_order_confirmation_with_valid_order_state';
    }

    public function getTargets(): string
    {
        return Constraint::CLASS_CONSTRAINT;
    }
}
