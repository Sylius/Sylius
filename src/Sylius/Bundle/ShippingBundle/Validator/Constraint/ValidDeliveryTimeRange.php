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

namespace Sylius\Bundle\ShippingBundle\Validator\Constraint;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class ValidDeliveryTimeRange extends Constraint
{
    /**
     * @param array<string, mixed>|null $options
     */
    #[HasNamedArguments]
    public function __construct(
        ?array $options = null,
        public string $message = 'sylius.form.shipping_method.max_delivery_time_days.greater_or_equal_min',
        ?array $groups = null,
        mixed $payload = null,
    ) {
        if (\is_array($options)) {
            trigger_deprecation(
                'sylius/shipping-bundle',
                '2.3',
                'Passing an array of options to configure the "%s" constraint is deprecated and will be removed in Sylius 3.0, use named arguments instead.',
                static::class,
            );

            $this->message = $options['message'] ?? $this->message;
            $groups ??= $options['groups'] ?? null;
            $payload ??= $options['payload'] ?? null;
        }

        parent::__construct(groups: $groups, payload: $payload);
    }

    public function validatedBy(): string
    {
        return 'sylius_shipping_method_valid_delivery_time_range';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
