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
    /**
     * @param array<string, mixed>|null $options
     */
    #[HasNamedArguments]
    public function __construct(
        ?array $options = null,
        ?string $message = null,
        ?string $notFoundMessage = null,
        ?string $shipmentNotFoundMessage = null,
        ?string $shippingAddressNotFoundMessage = null,
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

            $message ??= $options['message'] ?? null;
            $notFoundMessage ??= $options['notFoundMessage'] ?? null;
            $shipmentNotFoundMessage ??= $options['shipmentNotFoundMessage'] ?? null;
            $shippingAddressNotFoundMessage ??= $options['shippingAddressNotFoundMessage'] ?? null;
            $groups ??= $options['groups'] ?? null;
            $payload ??= $options['payload'] ?? null;
        }

        parent::__construct(groups: $groups, payload: $payload);

        $this->message = $message ?? $this->message;
        $this->notFoundMessage = $notFoundMessage ?? $this->notFoundMessage;
        $this->shipmentNotFoundMessage = $shipmentNotFoundMessage ?? $this->shipmentNotFoundMessage;
        $this->shippingAddressNotFoundMessage = $shippingAddressNotFoundMessage ?? $this->shippingAddressNotFoundMessage;
    }

    public string $message = 'sylius.shipping_method.not_available';

    public string $notFoundMessage = 'sylius.shipping_method.not_found';

    public string $shipmentNotFoundMessage = 'sylius.shipment.not_found';

    /** @var string */
    public $shippingAddressNotFoundMessage = 'sylius.shipping_method.shipping_address_not_found';

    public function validatedBy(): string
    {
        return 'sylius_api_validator_chosen_shipping_method_eligibility';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
