<?php

declare(strict_types=1);

namespace Sylius\Component\Taxation\Checker;

use Sylius\Component\Taxation\Model\TaxRateInterface;

interface TaxRateDateCheckerInterface
{
    public function check(array $taxRates): ?TaxRateInterface;
}
