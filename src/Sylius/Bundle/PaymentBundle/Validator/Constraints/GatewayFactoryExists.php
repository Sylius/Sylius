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
    /** @deprecated since Sylius 2.3, use $invalidGatewayFactoryMessage instead. It will be removed in Sylius 3.0. */
    public string $invalidGatewayFactory = 'sylius.gateway_config.invalid_gateway_factory';

    /**
     * @param array<string, mixed>|null $options
     */
    #[HasNamedArguments]
    public function __construct(
        ?array $options = null,
        public string $invalidGatewayFactoryMessage = 'sylius.gateway_config.invalid_gateway_factory',
        ?string $invalidGatewayFactory = null,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        if (\is_array($options)) {
            trigger_deprecation(
                'sylius/payment-bundle',
                '2.3',
                'Passing an array of options to configure the "%s" constraint is deprecated and will be removed in Sylius 3.0, use named arguments instead.',
                static::class,
            );

            $this->invalidGatewayFactoryMessage = $options['invalidGatewayFactoryMessage'] ?? $this->invalidGatewayFactoryMessage;
            $invalidGatewayFactory ??= $options['invalidGatewayFactory'] ?? null;
            $groups ??= $options['groups'] ?? null;
            $payload ??= $options['payload'] ?? null;
        }

        if (null !== $invalidGatewayFactory) {
            trigger_deprecation(
                'sylius/payment-bundle',
                '2.3',
                'The "invalidGatewayFactory" option of the "%s" constraint is deprecated and will be removed in Sylius 3.0, use "invalidGatewayFactoryMessage" instead.',
                static::class,
            );

            $this->invalidGatewayFactoryMessage = $invalidGatewayFactory;
        }

        parent::__construct(groups: $groups, payload: $payload);
        $this->invalidGatewayFactory = $this->invalidGatewayFactoryMessage;
    }

    public function validatedBy(): string
    {
        return 'sylius_gateway_factory_exists_validator';
    }

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
