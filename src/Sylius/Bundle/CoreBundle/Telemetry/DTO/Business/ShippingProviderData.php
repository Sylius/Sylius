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
    /** @var string */
    public $name;

    /** @var string */
    public $calculator;

    /** @var string */
    public $shipmentsCount;

    /** @var bool */
    public $enabled;

    public function __construct(string $name, string $calculator, string $shipmentsCount, bool $enabled)
    {
        $this->name = $name;
        $this->calculator = $calculator;
        $this->shipmentsCount = $shipmentsCount;
        $this->enabled = $enabled;
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
