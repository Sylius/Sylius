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

use Webmozart\Assert\Assert;

final class ChartFactory implements ChartFactoryInterface
{
    public function createTimeSeries(\DatePeriod $datePeriod, array $namedDatasets): ChartInterface
    {
        $this->validateDatasetsDataKeys($namedDatasets);

        return $this->createChart($datePeriod, $namedDatasets);
    }

    /**
     * @param array<string, array<array{total: int, year: int, month: int}>> $inputDatasets
     */
    private function createChart(\DatePeriod $datePeriod, array $inputDatasets): ChartInterface
    {
        $labels = [];
        $datasets = [];

        foreach ($datePeriod as $date) {
            $labels[] = $date->format('Y-m-d');

            foreach ($inputDatasets as $datasetName => $dataset) {
                Assert::isInstanceOf($date, \DateTimeImmutable::class);
                $datasets[$datasetName][] = $this->populateDataByDate($date, $dataset);
            }
        }

        return new Chart($labels, $datasets);
    }

    /** @param array<array-key, mixed> $ordersTotals */
    private function populateDataByDate(\DateTimeImmutable $date, array $ordersTotals): int
    {
        $year = (int) $date->format('Y');
        $month = (int) $date->format('n');

        foreach ($ordersTotals as $orderTotal) {
            if ((int) $orderTotal['year'] === $year && (int) $orderTotal['month'] === $month) {
                return (int) $orderTotal['total'];
            }
        }

        return 0;
    }

    /**
     * @param array<array-key, array<array-key, mixed>> $datasets
     */
    private function validateDatasetsDataKeys(array $datasets): void
    {
        foreach ($datasets as $dataset) {
            $this->validateDataKeysForDataset($dataset);
        }
    }

    /**
     * @param array<array-key, array<object>> $dataset
     */
    private function validateDataKeysForDataset(array $dataset): void
    {
        $requiredFields = ['total', 'year', 'month'];

        foreach ($dataset as $data) {
            Assert::isArray($data, sprintf('The dataset element must be an array, got %s.', get_debug_type($data)));
            $dataKeys = array_keys($data);

            foreach ($requiredFields as $field) {
                Assert::inArray($field, $dataKeys, "The data must contain the $field key.");
            }
        }
    }
}
