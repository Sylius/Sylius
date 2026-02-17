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
final class PaymentProviderData implements TelemetryDataInterface
{
    public function __construct(
        public string $name,
        public string $gateway,
        public string $paymentsCount,
    ) {
    }

    /** @return array<string, string> */
    public function normalize(): array
    {
        return [
            'name' => $this->name,
            'gateway' => $this->gateway,
            'payments_count' => $this->paymentsCount,
        ];
    }
}
