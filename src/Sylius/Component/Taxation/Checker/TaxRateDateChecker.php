<?php

declare(strict_types=1);

namespace Sylius\Component\Taxation\Checker;

use Sylius\Calendar\Provider\DateTimeProviderInterface;
use Sylius\Component\Taxation\Model\TaxRateInterface;

class TaxRateDateChecker implements TaxRateDateCheckerInterface
{
    public function __construct(
        protected DateTimeProviderInterface $calendar,
    ) {
    }

    public function check(array $taxRates): ?TaxRateInterface
    {
        $now = $this->calendar->now();

        /** @var TaxRateInterface $taxRate */
        foreach ($taxRates as $key => $taxRate) {
            if (!$taxRate->isInDate($now)) {
                unset($taxRates[$key]);
            }
        }

        $taxRates = array_values($taxRates);

        return (empty($taxRates)) ? null : $taxRates[0];
    }
}
