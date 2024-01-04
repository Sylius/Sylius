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

namespace Sylius\Bundle\PayumBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class GatewayFactoryExists extends Constraint
{
    public string $invalidGatewayFactory = 'sylius.gateway_config.invalid_gateway_factory';

    public function validatedBy(): string
    {
        return 'sylius_gateway_factory_exists_validator';
    }

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
