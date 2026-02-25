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
final class InstalledPluginsData implements TelemetryDataInterface
{
    /** @var list<PluginData> */
    public $plugins;

    public function __construct(PluginData ...$plugins)
    {
        $this->plugins = $plugins;
    }

    /** @return list<array<string, string>> */
    public function normalize(): array
    {
        return array_map(
            static fn (PluginData $plugin) => $plugin->normalize(),
            $this->plugins
        );
    }
}
