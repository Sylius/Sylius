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
    /** @var string */
    public $app;

    /** @var string|null */
    public $webserver;

    /** @var string */
    public $os;

    /** @var bool */
    public $docker;

    /** @var float|null */
    public $ramGb;

    /** @var string|null */
    public $phpMemoryLimit;

    public function __construct(
        string $app,
        ?string $webserver,
        string $os,
        bool $docker,
        ?float $ramGb,
        ?string $phpMemoryLimit
    ) {
        $this->app = $app;
        $this->webserver = $webserver;
        $this->os = $os;
        $this->docker = $docker;
        $this->ramGb = $ramGb;
        $this->phpMemoryLimit = $phpMemoryLimit;
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
