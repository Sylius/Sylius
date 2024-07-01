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

namespace Sylius\Bundle\AdminBundle\Twig\Component\Dashboard;

use Sylius\Bundle\AdminBundle\Provider\StatisticsDataProviderInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Statistics\Provider\StatisticsProviderInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent]
class StatisticsComponent
{
    use ComponentToolsTrait;
    use DefaultActionTrait;
    use HookableLiveComponentTrait;

    #[LiveProp]
    public string $channelCode;

    #[LiveProp]
    public string $startDate = 'first day of january this year';

    #[LiveProp]
    public string $endDate = 'first day of january next year';

    #[LiveProp]
    public string $period = 'year';

    #[LiveProp]
    public string $interval = 'month';

    /**
     * @param ChannelRepositoryInterface<ChannelInterface> $channelRepository
     */
    public function __construct(
        private readonly ChannelRepositoryInterface $channelRepository,
        private readonly StatisticsProviderInterface $statisticsProvider,
    )
    {
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Exception
     */
    #[ExposeInTemplate(name: 'statistics')]
    public function getStatistics(): array
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelRepository->findOneByCode($this->channelCode);

        $statistics = $this->statisticsProvider->provide(
            $this->interval,
            new \DatePeriod(new \DateTime($this->startDate), $this->resolveInterval(), new \DateTime($this->endDate)),
            $channel,
        );

        $result['business_activity_summary'] = $statistics->getBusinessActivitySummary();

        $saleList = $statistics->getSales();
        $result['sales_summary'] = [
            'intervals' => array_column($saleList, 'period'),
            'sales' => array_map(
                static function (int $total): string {
                    return number_format(abs($total / 100), 2, '.', '');
                },
                array_column($saleList, 'total'),
            ),
        ];

        return $result;
    }

    #[LiveAction]
    public function changeRange(
        #[LiveArg] string $period,
        #[LiveArg] string $interval,
    ): void
    {
        $this->period = $period;
        $this->interval = $interval;

        $this->resolveDates();
    }

    #[LiveAction]
    public function getPreviousPeriod(): void
    {
        $this->startDate = (new \DateTime($this->startDate))->sub(new \DateInterval($this->resolveChangePeriodInterval()))->format('Y-m-d');
        $this->endDate = (new \DateTime($this->endDate))->sub(new \DateInterval($this->resolveChangePeriodInterval()))->format('Y-m-d');
    }

    #[LiveAction]
    public function getNextPeriod(): void
    {
        $this->startDate = (new \DateTime($this->startDate))->add(new \DateInterval($this->resolveChangePeriodInterval()))->format('Y-m-d');
        $this->endDate = (new \DateTime($this->endDate))->add(new \DateInterval($this->resolveChangePeriodInterval()))->format('Y-m-d');
    }

    private function resolveInterval(): \DateInterval
    {
        $interval = match ($this->interval) {
            'day' => 'P1D',
            'month' => 'P1M',
            default => throw new \InvalidArgumentException(sprintf('Interval "%s" is not supported.', $this->interval)),
        };

        return new \DateInterval($interval);
    }

    private function resolveDates(): void
    {
        [$startDate, $endDate] = match ($this->period) {
            'year' => [
                (new \DateTime('first day of January this year'))->format('Y-m-d'),
                (new \DateTime('first day of January next year'))->format('Y-m-d'),
            ],
            'month' => [
                (new \DateTime('first day of this month'))->format('Y-m-d'),
                (new \DateTime('first day of next month'))->format('Y-m-d'),
            ],
            '2 weeks' => [
                (new \DateTime('monday previous week'))->format('Y-m-d'),
                (new \DateTime('monday next week'))->format('Y-m-d'),
            ],
            default => throw new \InvalidArgumentException(sprintf('Period "%s" is not supported.', $this->period)),
        };

        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    private function resolveChangePeriodInterval(): string
    {
        return match ($this->period) {
            'year' => 'P1Y',
            'month' => 'P1M',
            '2 weeks' => 'P2W',
            default => throw new \InvalidArgumentException(sprintf('Period "%s" is not supported.', $this->period)),
        };
    }
}
