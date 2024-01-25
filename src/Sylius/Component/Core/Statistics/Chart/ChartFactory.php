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

namespace Sylius\Component\Core\Statistics\Chart;

final class ChartFactory implements ChartFactoryInterface
{
    /** @var array<string, string> */
    private array $formatsMap = [];

    /** @param array<string, array{interval: string, period_format: string}> $intervalsMap */
    public function __construct(array $intervalsMap)
    {
        foreach ($intervalsMap as $type => $intervalMap) {
            $this->formatsMap[$type] = $intervalMap['period_format'];
        }
    }

    public function createTimeSeries(string $intervalType, string $datasetName, array $datasets): ChartInterface
    {
        return $this->createChart($datasetName, $datasets, $this->formatsMap[$intervalType]);
    }

    /** @param array<array{period: \DateTimeInterface, total: int}> $datasets */
    private function createChart(string $datasetName, array $datasets, string $dateFormat): ChartInterface
    {
        $labels = array_map(
            fn (\DateTimeInterface $date) => $date->format($dateFormat),
            array_column($datasets, 'period'),
        );
        $resultDatasets = array_column($datasets, 'total');

        return new Chart($labels, [$datasetName => $resultDatasets]);
    }
}
