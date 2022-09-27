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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Updater;

use Sylius\Component\Core\Model\TaxRateInterface;

final class TaxRateUpdater implements TaxRateUpdaterInterface
{
    public function update(TaxRateInterface $taxRate, array $attributes): void
    {
        $taxRate->setCode($attributes['code']);
        $taxRate->setName($attributes['name']);
        $taxRate->setAmount($attributes['amount']);
        $taxRate->setIncludedInPrice($attributes['included_in_price']);
        $taxRate->setCalculator($attributes['calculator']);
        $taxRate->setZone($attributes['zone']);
        $taxRate->setCategory($attributes['category']);
    }
}
