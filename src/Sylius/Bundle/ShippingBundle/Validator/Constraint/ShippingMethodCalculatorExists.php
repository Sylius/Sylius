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
final class ShippingMethodCalculatorExists extends Constraint
{
    public string $invalidShippingCalculatorMessage = 'sylius.shipping_method.calculator.invalid';

    /** @deprecated since Sylius 2.3, use $invalidShippingCalculatorMessage instead. It will be removed in Sylius 3.0. */
    public string $invalidShippingCalculator = 'sylius.shipping_method.calculator.invalid';

    /**
     * @param array<string, mixed>|null $options
     * @param array<string>|null $groups
     */
    #[HasNamedArguments]
    public function __construct(
        ?array $options = null,
        ?string $invalidShippingCalculatorMessage = null,
        ?string $invalidShippingCalculator = null,
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

            $invalidShippingCalculatorMessage ??= $options['invalidShippingCalculatorMessage'] ?? null;
            $invalidShippingCalculator ??= $options['invalidShippingCalculator'] ?? null;
            $groups ??= $options['groups'] ?? null;
            $payload ??= $options['payload'] ?? null;
        }

        if (null !== $invalidShippingCalculator) {
            trigger_deprecation(
                'sylius/shipping-bundle',
                '2.3',
                'The "invalidShippingCalculator" option of the "%s" constraint is deprecated and will be removed in Sylius 3.0, use "invalidShippingCalculatorMessage" instead.',
                static::class,
            );

            $invalidShippingCalculatorMessage ??= $invalidShippingCalculator;
        }

        parent::__construct(groups: $groups, payload: $payload);

        $this->invalidShippingCalculatorMessage = $invalidShippingCalculatorMessage ?? $this->invalidShippingCalculatorMessage;
        $this->invalidShippingCalculator = $this->invalidShippingCalculatorMessage;
    }

    public function validatedBy(): string
    {
        return 'sylius_shipping_method_calculator_exists';
    }

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
