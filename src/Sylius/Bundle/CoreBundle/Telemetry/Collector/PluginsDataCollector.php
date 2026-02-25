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

namespace Sylius\Bundle\CoreBundle\Telemetry\Collector;

use Sylius\Component\Core\Telemetry\Collector\TelemetryDataCollectorInterface;
use Sylius\Component\Core\Telemetry\DataProvider\DataProviderInterface;

/** @internal */
final class PluginsDataCollector implements TelemetryDataCollectorInterface
{
    /** @var iterable<DataProviderInterface> */
    private $dataProviders;

    /** @var bool */
    private $enabled;

    /**
     * @param iterable<DataProviderInterface> $dataProviders
     */
    public function __construct(iterable $dataProviders, bool $enabled = true)
    {
        $this->dataProviders = $dataProviders;
        $this->enabled = $enabled;
    }

    public function getName(): string
    {
        return 'plugins';
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function collect(): array
    {
        $pluginsData = [];

        foreach ($this->dataProviders as $dataProvider) {
            if (!$dataProvider instanceof DataProviderInterface) {
                continue;
            }

            try {
                $dto = $dataProvider->provide();
                $pluginsData = array_merge($pluginsData, $dto->normalize());
            } catch (\Throwable $e) {
            }
        }

        return $pluginsData;
    }
}
