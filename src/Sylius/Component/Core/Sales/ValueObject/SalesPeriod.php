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

namespace Sylius\Component\Core\Sales\ValueObject;

class SalesPeriod
{
    private \DatePeriod $period;

    public function __construct(\DateTimeInterface $startDate, \DateTimeInterface $endDate, \DateInterval $interval)
    {
        if ($startDate >= $endDate) {
            throw new \InvalidArgumentException('Start date cannot be greater or equal to end date.');
        }

        $this->period = new \DatePeriod($startDate, $interval, $endDate);
    }

    public function getStartDate(): \DateTimeInterface
    {
        return $this->period->getStartDate();
    }

    public function getEndDate(): \DateTimeInterface
    {
        return $this->period->getEndDate();
    }

    public function getInterval(): string
    {
        $interval = $this->period->getDateInterval();

        if ($interval->y > 0) {
            return 'year';
        }

        if ($interval->m > 0) {
            return 'month';
        }

        if ($interval->d >= 7) {
            return 'week';
        }

        return 'day';
    }
}
