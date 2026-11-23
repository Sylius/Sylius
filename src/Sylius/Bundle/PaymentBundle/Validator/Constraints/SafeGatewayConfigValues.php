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

namespace Sylius\Bundle\PaymentBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class SafeGatewayConfigValues extends Constraint
{
    public string $forbiddenValueMessage = 'sylius.gateway_config.config.forbidden_value';

    public function validatedBy(): string
    {
        return 'sylius_safe_gateway_config_values_validator';
    }
}
