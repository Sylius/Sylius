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
    /** @var string */
    public $name;

    /** @var string */
    public $gateway;

    /** @var string */
    public $paymentsCount;

    public function __construct(string $name, string $gateway, string $paymentsCount)
    {
        $this->name = $name;
        $this->gateway = $gateway;
        $this->paymentsCount = $paymentsCount;
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
