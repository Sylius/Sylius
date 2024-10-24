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
use Webmozart\Assert\Assert;

final class SalesStatisticsProvider implements SalesStatisticsProviderInterface
{
    /** @var array<string, string> */
    private array $formatsMap = [];

    /** @param array<string, array{interval: string, period_format: string}> $intervalsMap */
    public function __construct(
        private OrdersTotalsProviderRegistryInterface $ordersTotalsProviderRegistry,
        array $intervalsMap,
    ) {
        foreach ($intervalsMap as $type => $intervalMap) {
            $this->formatsMap[$type] = $intervalMap['period_format'];
        }
    }

    public function provide(string $intervalType, \DatePeriod $datePeriod, ChannelInterface $channel): array
    {
        $format = $this->getPeriodFormat($intervalType);

        $sales = $this->ordersTotalsProviderRegistry
            ->getByType($intervalType)
            ->provideForPeriodInChannel($datePeriod, $channel)
        ;

        return $this->withFormattedDates($sales, $format);
    }

    /**
     * @param array<array{total: int, period: \DateTimeInterface}> $sales
     *
     * @return array<array{total: int, period: string}>
     */
    private function withFormattedDates(array $sales, string $format): array
    {
        return array_map(fn (array $entry) => [
            'period' => $entry['period']->format($format),
            'total' => $entry['total'],
        ], $sales);
    }

    private function getPeriodFormat(string $intervalType): string
    {
        Assert::keyExists($this->formatsMap, $intervalType);

        return $this->formatsMap[$intervalType];
    }
}
