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
final class ShippingProviderData implements TelemetryDataInterface
{
    public function __construct(
        public string $name,
        public string $calculator,
        public string $shipmentsCount,
        public bool $enabled,
    ) {
    }

    /** @return array<string, bool|string> */
    public function normalize(): array
    {
        return [
            'name' => $this->name,
            'calculator' => $this->calculator,
            'shipments_count' => $this->shipmentsCount,
            'enabled' => $this->enabled,
        ];
    }
}
