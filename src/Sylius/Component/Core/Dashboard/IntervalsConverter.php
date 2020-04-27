<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Dashboard;

final class IntervalsConverter implements IntervalsConverterInterface
{
    public function getIntervals(
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate,
        string $intervalName
    ): \DatePeriod {
        $supportedIntervals = [
            'hour' => '1 hour',
            'month' => '1 month',
            'year' => '1 year',
            'day' => '1 day'
        ];

        if (!array_key_exists($intervalName, $supportedIntervals)) {
            throw new \InvalidArgumentException(sprintf('%s is a not supported interval', $intervalName));
        }
        if ($startDate >= $endDate) {
            throw new \InvalidArgumentException('endDate should be later then startDate');
        }

        return new \DatePeriod($startDate, \DateInterval::createFromDateString($supportedIntervals[$intervalName]), $endDate);
    }
}
