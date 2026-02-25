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

namespace Sylius\Bundle\CoreBundle\Telemetry\DTO\Technical;

use Sylius\Component\Core\Telemetry\DTO\TelemetryDataInterface;

/** @internal */
final class DatabaseData implements TelemetryDataInterface
{
    public function __construct(
        public ?string $type,
        public ?string $version,
    ) {
    }

    public function normalize(): array
    {
        return [
            'database' => [
                'type' => $this->type,
                'version' => $this->version,
            ],
        ];
    }
}
