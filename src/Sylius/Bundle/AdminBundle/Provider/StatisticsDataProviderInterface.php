<?php

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Provider;

use Sylius\Component\Core\Model\ChannelInterface;

interface StatisticsDataProviderInterface
{
    public function getRawData(ChannelInterface $channel, \DateTime $startDate, \DateTime $endDate, string $interval): array;
}
