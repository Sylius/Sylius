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

namespace Sylius\Bundle\CoreBundle\Telemetry\DTO\Business;

use Sylius\Component\Core\Telemetry\DTO\TelemetryDataInterface;

/** @internal */
final class ShippingMethodsData implements TelemetryDataInterface
{
    /** @var list<ShippingProviderData> */
    public $shippingProviders;

    public function __construct(ShippingProviderData ...$shippingProviders)
    {
        $this->shippingProviders = $shippingProviders;
    }

    public function normalize(): array
    {
        return [
            'shipping_providers' => array_map(
                static fn (ShippingProviderData $provider) => $provider->normalize(),
                $this->shippingProviders
            ),
        ];
    }
}
