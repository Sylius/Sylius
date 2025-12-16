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
final class TechnicalDataCollector implements TelemetryDataCollectorInterface
{
    /**
     * @param iterable<DataProviderInterface> $dataProviders
     */
    public function __construct(
        private iterable $dataProviders,
        private bool $enabled = true,
    ) {
    }

    public function getName(): string
    {
        return 'technical';
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function collect(): array
    {
        $technicalData = [];

        foreach ($this->dataProviders as $dataProvider) {
            if (!$dataProvider instanceof DataProviderInterface) {
                continue;
            }

            try {
                $dto = $dataProvider->provide();
                $technicalData = array_merge($technicalData, $dto->normalize());
            } catch (\Throwable) {
            }
        }

        return $technicalData;
    }
}
