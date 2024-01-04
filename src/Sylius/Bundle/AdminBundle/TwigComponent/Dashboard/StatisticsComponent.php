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

namespace Sylius\Bundle\AdminBundle\TwigComponent\Dashboard;

use Sylius\Bundle\AdminBundle\Provider\StatisticsDataProviderInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent]
final class StatisticsComponent
{
    use ComponentToolsTrait;
    use DefaultActionTrait;

    #[ExposeInTemplate]
    public readonly string $eventName;

    #[LiveProp]
    public string $channelCode;

    #[LiveProp]
    #[ExposeInTemplate]
    public string $startDate = 'first day of january this year';

    #[LiveProp]
    #[ExposeInTemplate]
    public string $endDate = 'first day of january next year';

    #[LiveProp]
    #[ExposeInTemplate]
    public string $range = 'month';

    #[LiveProp]
    #[ExposeInTemplate]
    public string $rangeName = 'year';

    /**
     * @param ChannelRepositoryInterface<ChannelInterface> $channelRepository
     */
    public function __construct (
        private readonly ChannelRepositoryInterface $channelRepository,
        private readonly StatisticsDataProviderInterface $statisticsDataProvider,
    ) {
        $this->eventName = 'sylius.admin.dashboard.statistics';
    }

    /**
     * @return array<string, mixed>
     */
    #[ExposeInTemplate]
    public function getStatistics(): array
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelRepository->findOneByCode($this->channelCode);

        return $this->statisticsDataProvider->getRawData(
            $channel,
            new \DateTime($this->startDate),
            new \DateTime($this->endDate),
            $this->range,
        );
    }

    #[LiveAction]
    public function changeRange(
        #[LiveArg] string $name,
        #[LiveArg] string $range,
        #[LiveArg] string $startDate,
        #[LiveArg] string $endDate
    ): void {
        $this->rangeName = $name;
        $this->range = $range;
        $this->startDate = date('Y-m-d', strtotime($startDate));
        $this->endDate = date('Y-m-d', strtotime($endDate));
    }
}
