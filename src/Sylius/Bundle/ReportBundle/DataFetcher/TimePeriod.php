<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReportBundle\DataFetcher;

use Sylius\Component\Report\DataFetcher\Data;
use Sylius\Component\Report\DataFetcher\DataFetcherInterface;

/**
 * Abstract class to provide time periods logic.
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
abstract class TimePeriod implements DataFetcherInterface
{
    const PERIOD_DAY = 'day';
    const PERIOD_MONTH = 'month';
    const PERIOD_YEAR = 'year';

    /**
     * @return array
     */
    public static function getPeriodChoices()
    {
        return [
            self::PERIOD_DAY => 'Daily',
            self::PERIOD_MONTH => 'Monthly',
            self::PERIOD_YEAR => 'Yearly',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(array $configuration)
    {
        $data = new Data();

        //There is added 23 hours 59 minutes 59 seconds to the end date to provide records for whole end date
        $configuration['end'] = $configuration['end']->add(new \DateInterval('PT23H59M59S'));
        //This should be removed after implementation hourly periods

        switch ($configuration['period']) {
            case self::PERIOD_DAY:
                $this->setExtraConfiguration($configuration, 'P1D', '%a', 'Y-m-d', ['date']);
                break;
            case self::PERIOD_MONTH:
                $this->setExtraConfiguration($configuration, 'P1M', '%m', 'F Y', ['month', 'year']);
                break;
            case self::PERIOD_YEAR:
                $this->setExtraConfiguration($configuration, 'P1Y', '%y', 'Y', ['year']);
                break;
            default:
                throw new \InvalidArgumentException('Wrong data fetcher period');
        }

        $rawData = $this->getData($configuration);

        if (empty($rawData)) {
            return $data;
        }

        $labels = array_keys($rawData[0]);
        $data->setLabels($labels);

        $fetched = [];

        if ($configuration['empty_records']) {
            $fetched = $this->fillEmptyRecords($fetched, $configuration);
        }
        foreach ($rawData as $row) {
            $date = new \DateTime($row[$labels[0]]);
            $fetched[$date->format($configuration['presentationFormat'])] = $row[$labels[1]];
        }

        $data->setData($fetched);

        return $data;
    }

    /**
     * Responsible for providing raw data to fetch, from the configuration (ie: start date, end date, time period,
     * empty records flag, interval, period format, presentation format, group by).
     *
     * @param array $configuration
     *
     * @return array
     */
    abstract protected function getData(array $configuration = []);

    /**
     * @param array  $configuration
     * @param string $interval
     * @param string $periodFormat
     * @param string $presentationFormat
     * @param array  $groupBy
     */
    private function setExtraConfiguration(
        array &$configuration,
        $interval,
        $periodFormat,
        $presentationFormat,
        array $groupBy
    ) {
        $configuration['interval'] = $interval;
        $configuration['periodFormat'] = $periodFormat;
        $configuration['presentationFormat'] = $presentationFormat;
        $configuration['groupBy'] = $groupBy;
    }

    /**
     * @param array $fetched
     * @param array $configuration
     *
     * @return array
     */
    private function fillEmptyRecords(array $fetched, array $configuration)
    {
        $date = $configuration['start'];
        $dateInterval = new \DateInterval($configuration['interval']);

        $numberOfPeriods = $configuration['start']->diff($configuration['end']);
        $formattedNumberOfPeriods = $numberOfPeriods->format($configuration['periodFormat']);

        for ($i = 0; $i <= $formattedNumberOfPeriods; ++$i) {
            $fetched[$date->format($configuration['presentationFormat'])] = 0;
            $date = $date->add($dateInterval);
        }

        return $fetched;
    }
}
