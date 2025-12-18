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

namespace Sylius\Component\Core\Statistics\Provider;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Statistics\Registry\OrdersTotalsProviderRegistryInterface;
use Sylius\Component\Core\Statistics\Registry\StatisticsProviderRegistryInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Webmozart\Assert\Assert;

final class SalesStatisticsProvider implements SalesStatisticsProviderInterface
{
    /** @var array<string, string> */
    private array $formatsMap = [];

    /** @var array<StatisticsProviderRegistryInterface> */
    private array $statisticsProviderRegistries = [];

    /**
     * @param array<string, array{interval: string, period_format: string}> $intervalsMap
     * @param iterable<StatisticsProviderRegistryInterface> $statisticsProviderRegistries
     */
    public function __construct(
        private readonly OrdersTotalsProviderRegistryInterface $ordersTotalsProviderRegistry,
        array $intervalsMap,
        iterable $statisticsProviderRegistries = [],
        private readonly ?CacheInterface $cache = null,
        private readonly int $cacheExpiresAfter = 1800,
    ) {
        foreach ($intervalsMap as $type => $intervalMap) {
            $this->formatsMap[$type] = $intervalMap['period_format'];
        }

        if ($cache === null) {
            trigger_deprecation(
                'sylius/core',
                '2.1',
                'Not passing $cache through constructor is deprecated and will be prohibited in Sylius 3.0.',
            );
        }

        if ($statisticsProviderRegistries === []) {
            trigger_deprecation(
                'sylius/core',
                '2.1',
                'Not passing $statisticsProviderRegistries through constructor is deprecated and will be prohibited in Sylius 3.0.',
            );

            return;
        }

        Assert::allIsInstanceOf(
            $statisticsProviderRegistries,
            StatisticsProviderRegistryInterface::class,
            sprintf('All statistics provider registries should implement the "%s" interface.', StatisticsProviderRegistryInterface::class),
        );
        $this->statisticsProviderRegistries = $statisticsProviderRegistries instanceof \Traversable ? iterator_to_array($statisticsProviderRegistries) : $statisticsProviderRegistries;
    }

    public function provide(string $intervalType, \DatePeriod $datePeriod, ChannelInterface $channel): array
    {
        $format = $this->getPeriodFormat($intervalType);

        if ($this->cache === null) {
            return $this->getStatisticsData($intervalType, $datePeriod, $channel, $format);
        }

        return $this->cache->get(
            $this->buildCacheKey($intervalType, $datePeriod, $channel),
            function (ItemInterface $item) use ($intervalType, $datePeriod, $channel, $format): array {
                $item->expiresAfter($this->cacheExpiresAfter);

                return $this->getStatisticsData($intervalType, $datePeriod, $channel, $format);
            },
        );
    }

    /** @return array<array{period: string, ...}> */
    private function getStatisticsData(
        string $intervalType,
        \DatePeriod $datePeriod,
        ChannelInterface $channel,
        string $format,
    ): array {
        if ($this->statisticsProviderRegistries === []) {
            $sales = $this->ordersTotalsProviderRegistry
                ->getByType($intervalType)
                ->provideForPeriodInChannel($datePeriod, $channel)
            ;

            return $this->withFormattedDates($sales, $format);
        }

        $data = [];
        foreach ($this->statisticsProviderRegistries as $statisticsProviderRegistry) {
            $result = $statisticsProviderRegistry
                ->getByType($intervalType)
                ->provideForPeriodInChannel($datePeriod, $channel)
            ;

            $data = $data === [] ? $result : $this->mergeArraysByPeriod($data, $result);
        }

        return $this->withFormattedDates($data, $format);
    }

    private function buildCacheKey(string $intervalType, \DatePeriod $datePeriod, ChannelInterface $channel): string
    {
        $start = $datePeriod->getStartDate()->format('YmdH');
        $end = $datePeriod->getEndDate()?->format('YmdH') ?? 'null';

        return sprintf('sylius_sales_statistics.%s.%s.%s.%s', $intervalType, $start, $end, $channel->getCode());
    }

    /**
     * @param array<array{period: \DateTimeInterface, ...}> $sales
     *
     * @return array<array{period: string, ...}>
     */
    private function withFormattedDates(array $sales, string $format): array
    {
        return array_map(
            fn (array $entry) => array_merge(
                ['period' => $entry['period']->format($format)],
                array_diff_key($entry, ['period' => true]),
            ),
            $sales,
        );
    }

    private function getPeriodFormat(string $intervalType): string
    {
        Assert::keyExists($this->formatsMap, $intervalType);

        return $this->formatsMap[$intervalType];
    }

    /**
     * @param array<array{period: \DateTimeInterface, ...}> $firstArray
     * @param array<array{period: \DateTimeInterface, ...}> $secondArray
     *
     * @return array<array{period: \DateTimeInterface, ...}>
     */
    private function mergeArraysByPeriod(array $firstArray, array $secondArray): array
    {
        return iterator_to_array($this->generateMergedByPeriod($firstArray, $secondArray));
    }

    /**
     * @param array<array{period: \DateTimeInterface, ...}> $firstArray
     * @param array<array{period: \DateTimeInterface, ...}> $secondArray
     *
     * @return \Generator<array{period: \DateTimeInterface, ...}>
     */
    private function generateMergedByPeriod(array $firstArray, array $secondArray): \Generator
    {
        [$indexedFirstArray, $firstArrayKeys] = $this->indexByPeriodWithKeys($firstArray);
        [$indexedSecondArray, $secondArrayKeys] = $this->indexByPeriodWithKeys($secondArray);

        $allKeys = array_keys(array_merge(
            array_fill_keys($firstArrayKeys, true),
            array_fill_keys($secondArrayKeys, true),
        ));

        foreach ($allKeys as $key) {
            $itemFromFirstArray = $indexedFirstArray[$key] ?? [];
            $itemFromSecondArray = $indexedSecondArray[$key] ?? [];

            $period = $itemFromFirstArray['period'] ?? $itemFromSecondArray['period'] ?? null;
            Assert::isInstanceOf($period, \DateTimeInterface::class);

            unset($itemFromFirstArray['period'], $itemFromSecondArray['period']);

            yield array_merge(
                ['period' => $period],
                $itemFromFirstArray,
                $itemFromSecondArray,
            );
        }
    }

    /**
     * @param array<array{period: \DateTimeInterface, ...}> $items
     *
     * @return array{0: array<string, array{period: \DateTimeInterface, ...}>, 1: string[]}
     */
    private function indexByPeriodWithKeys(array $items): array
    {
        $result = [];
        $keys = [];

        foreach ($items as $item) {
            $period = $item['period'] ?? null;
            Assert::isInstanceOf($period, \DateTimeInterface::class);

            $key = $period->format('c');
            $result[$key] = $item;
            $keys[] = $key;
        }

        return [$result, $keys];
    }
}
