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

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class ChosenShippingMethodEligibility extends Constraint
{
    public const SHIPPING_METHOD_NOT_AVAILABLE_ERROR = 'SHIPPING_METHOD_NOT_AVAILABLE';

    public const SHIPPING_METHOD_NOT_FOUND_ERROR = 'SHIPPING_METHOD_NOT_FOUND';

    public const SHIPMENT_NOT_FOUND_ERROR = 'SHIPMENT_NOT_FOUND';

    public const SHIPPING_ADDRESS_NOT_FOUND_ERROR = 'SHIPPING_ADDRESS_NOT_FOUND';

    /**
     * @param array<string, mixed>|null $options
     */
    #[HasNamedArguments]
    public function __construct(
        ?array $options = null,
        public string $message = 'sylius.shipping_method.not_available',
        public string $notFoundMessage = 'sylius.shipping_method.not_found',
        public string $shipmentNotFoundMessage = 'sylius.shipment.not_found',
        public string $shippingAddressNotFoundMessage = 'sylius.shipping_method.shipping_address_not_found',
        ?array $groups = null,
        mixed $payload = null,
    ) {
        if (\is_array($options)) {
            trigger_deprecation(
                'sylius/api-bundle',
                '2.3',
                'Passing an array of options to configure the "%s" constraint is deprecated and will be removed in Sylius 3.0, use named arguments instead.',
                static::class,
            );

            $this->message = $options['message'] ?? $this->message;
            $this->notFoundMessage = $options['notFoundMessage'] ?? $this->notFoundMessage;
            $this->shipmentNotFoundMessage = $options['shipmentNotFoundMessage'] ?? $this->shipmentNotFoundMessage;
            $this->shippingAddressNotFoundMessage = $options['shippingAddressNotFoundMessage'] ?? $this->shippingAddressNotFoundMessage;
            $groups ??= $options['groups'] ?? null;
            $payload ??= $options['payload'] ?? null;
        }

        parent::__construct(groups: $groups, payload: $payload);
    }

    public function validatedBy(): string
    {
        return 'sylius_api_validator_chosen_shipping_method_eligibility';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
