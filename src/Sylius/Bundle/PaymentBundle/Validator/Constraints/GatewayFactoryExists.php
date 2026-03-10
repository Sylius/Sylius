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

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class GatewayFactoryExists extends Constraint
{

    #[HasNamedArguments]
    public function __construct(
        string $invalidGatewayFactory = 'sylius.gateway_config.invalid_gateway_factory',
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct(groups: $groups, payload: $payload);

        $this->invalidGatewayFactory = $invalidGatewayFactory;
    }

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
