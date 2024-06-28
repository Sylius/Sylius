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
            new \DatePeriod(new \DateTime($this->startDate), new \DateInterval('P1M'), new \DateTime($this->endDate)),
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
    }

    #[LiveAction]
    public function getPreviousPeriod(): void
    {
        $this->startDate = date('Y-m-d', strtotime($this->startDate . ' -1 ' . $this->period));
        $this->endDate = date('Y-m-d', strtotime($this->endDate . ' -1 ' . $this->period));
    }

    #[LiveAction]
    public function getNextPeriod(): void
    {
        $this->startDate = date('Y-m-d', strtotime($this->startDate . ' +1 ' . $this->period));
        $this->endDate = date('Y-m-d', strtotime($this->endDate . ' +1 ' . $this->period));
    }
}
