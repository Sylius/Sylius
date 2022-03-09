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

namespace Sylius\Component\Core\Distributor;

use Webmozart\Assert\Assert;

final class ProportionalIntegerDistributor implements ProportionalIntegerDistributorInterface
{
    public function distribute(array $integers, int $amount): array
    {
        Assert::allInteger($integers);

        $total = array_sum($integers);
        $distributedAmounts = [];

        foreach ($integers as $element) {
            if ($element === 0) {
                $distributedAmounts[] = 0;
            } else {
                $distributedAmounts[] = (int) round(($element * $amount) / $total, 0, \PHP_ROUND_HALF_DOWN);
            }
        }

        if(array_sum($distributedAmounts) === 0) {
            return $distributedAmounts;
        }

        $missingAmount = $amount - array_sum($distributedAmounts);
        for ($i = 0, $iMax = abs($missingAmount); $i < $iMax; ++$i) {
            $distributedAmounts[$i] += $missingAmount >= 0 ? 1 : -1;
        }

        return $distributedAmounts;
    }
}
