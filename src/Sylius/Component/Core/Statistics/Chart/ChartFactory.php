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

use Sylius\Component\Core\DateTime\Period;
use Webmozart\Assert\Assert;

final class ChartFactory implements ChartFactoryInterface
{
    public function createTimeSeries(Period $period, array $namedDatasets): ChartInterface
    {
        $this->validateDatasetsDataKeys($namedDatasets);

        return $this->createChart($period, $namedDatasets);
    }

    /**
     * @param array<string, array<object>> $inputDatasets
     */
    private function createChart(Period $period, array $inputDatasets): ChartInterface
    {
        $period = $this->getDatePeriod($period);

        $labels = [];
        $datasets = [];

        foreach ($period as $date) {
            $labels[] = $date->format('Y-m-d');

            foreach ($inputDatasets as $datasetName => $dataset) {
                $datasets[$datasetName][] = $this->populateDataByDate($date, $dataset);
            }
        }

        return new Chart($labels, $datasets);
    }

    /** @param array<array-key, mixed> $ordersTotals */
    private function populateDataByDate(\DateTimeInterface $date, array $ordersTotals): int
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

    private function getDatePeriod(Period $period): \DatePeriod
    {
        return new \DatePeriod(
            $period->getStartDate(),
            \DateInterval::createFromDateString(sprintf('1 %s', $period->getIntervalType())),
            $period->getEndDate(),
        );
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
        $requiredFields = ['year', 'month'];

        foreach ($dataset as $data) {
            Assert::isArray($data, sprintf('The dataset element must be an array, got %s.', get_debug_type($data)));
            $dataKeys = array_keys($data);

            foreach ($requiredFields as $field) {
                Assert::inArray($field, $dataKeys, "The data must contain the $field key.");
            }
        }
    }
}
