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

namespace Sylius\Bundle\ApiBundle\Query;

class GetStatistics
{
    public function __construct(
        private string $intervalType,
        private \DatePeriod $datePeriod,
        private string $channelCode,
    ) {
    }

    public function getIntervalType(): string
    {
        return $this->intervalType;
    }

    public function getDatePeriod(): \DatePeriod
    {
        return $this->datePeriod;
    }

    public function getChannelCode(): string
    {
        return $this->channelCode;
    }
}
