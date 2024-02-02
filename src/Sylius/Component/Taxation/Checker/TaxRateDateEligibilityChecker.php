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

namespace Sylius\Component\Taxation\Checker;

use Sylius\Component\Taxation\Model\TaxRateInterface;
use Symfony\Component\Clock\ClockInterface;

final class TaxRateDateEligibilityChecker implements TaxRateDateEligibilityCheckerInterface
{
    public function __construct(
        protected ClockInterface $clock,
    ) {
    }

    public function isEligible(TaxRateInterface $taxRate): bool
    {
        $date = $this->clock->now();
        $startDate = $taxRate->getStartDate();
        $endDate = $taxRate->getEndDate();

        return (null === $startDate || $startDate <= $date) && (null === $endDate || $endDate >= $date);
    }
}
