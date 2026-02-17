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

namespace Sylius\Bundle\CoreBundle\Telemetry\DTO\Plugins;

use Sylius\Component\Core\Telemetry\DTO\TelemetryDataInterface;

/** @internal */
final class PluginData implements TelemetryDataInterface
{
    /** @var string */
    public $name;

    /** @var string */
    public $version;

    public function __construct(string $name, string $version)
    {
        $this->name = $name;
        $this->version = $version;
    }

    /** @return array<string, string> */
    public function normalize(): array
    {
        return [
            'name' => $this->name,
            'version' => $this->version,
        ];
    }
}
