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

class SalesInPeriod
{
    public function __construct(
        private int $total,
        private \DateTimeInterface $period,
    ) {
    }

    public function getPeriod(): \DateTimeInterface
    {
        return $this->period;
    }

    public function getTotal(): int
    {
        return $this->total;
    }
}
