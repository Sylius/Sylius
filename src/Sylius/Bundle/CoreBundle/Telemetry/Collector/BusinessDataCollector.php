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
final class BusinessDataCollector implements TelemetryDataCollectorInterface
{
    private const METRICS_KEYS = [
        'channels_count',
        'customers_count',
        'products_count',
        'product_variants_count',
        'virtual_product_variants_count',
        'orders_count',
        'orders_monthly_count',
        'orders_monthly_avg_items',
        'orders_monthly_avg_item_units',
    ];

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
        return 'business';
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function collect(): array
    {
        $data = [];
        $metrics = [];

        foreach ($this->dataProviders as $dataProvider) {
            if (!$dataProvider instanceof DataProviderInterface) {
                continue;
            }

            try {
                $dto = $dataProvider->provide();
                $normalized = $dto->normalize();

                [$metricsData, $otherData] = $this->separateMetrics($normalized);
                $metrics = array_merge($metrics, $metricsData);
                $data = array_merge($data, $otherData);
            } catch (\Throwable) {
                continue;
            }
        }

        if (!empty($metrics)) {
            $data['metrics'] = $metrics;
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $normalized
     * @return array{0: array<string, mixed>, 1: array<string, mixed>}
     */
    private function separateMetrics(array $normalized): array
    {
        $metrics = [];
        $other = [];

        foreach ($normalized as $key => $value) {
            if ($key === 'order_metrics' && is_array($value)) {
                $metrics = array_merge($metrics, $value);
            } elseif (in_array($key, self::METRICS_KEYS, true)) {
                $metrics[$key] = $value;
            } else {
                $other[$key] = $value;
            }
        }

        return [$metrics, $other];
    }
}
