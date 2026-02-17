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
final class EnvironmentData implements TelemetryDataInterface
{
    public function __construct(
        public string $app,
        public ?string $webserver,
        public string $os,
        public bool $docker,
        public ?float $ramGb,
        public ?string $phpMemoryLimit,
    ) {
    }

    public function normalize(): array
    {
        return [
            'environment' => [
                'app' => $this->app,
                'webserver' => $this->webserver,
                'os' => $this->os,
                'docker' => $this->docker,
                'ram_gb' => $this->ramGb,
                'php_memory_limit' => $this->phpMemoryLimit,
            ],
        ];
    }
}
