<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Distributor;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ProportionalIntegerDistributor implements ProportionalIntegerDistributorInterface
{
    /**
     * {@inheritdoc}
     */
    public function distribute($total, array $elements, $amount)
    {
        if ($total !== array_sum($elements)) {
            throw new \InvalidArgumentException('Element sum should be equal with total.');
        }

        $distributedAmounts = [];

        $sign = $amount < 0 ? -1 : 1;
        $amount = abs($amount);

        $distributedSum = 0;
        foreach ($elements as $element) {
            $distributedAmount = (int) ($sign * floor(($element * $amount) / $total));
            $distributedSum += $distributedAmount;

            $distributedAmounts[] = $distributedAmount;
        }

        if (abs($distributedSum) < $amount) {
            for ($i = 0; $i < ($amount - abs($distributedSum)); $i++) {
                (1 === $sign) ? $distributedAmounts[$i]++ : $distributedAmounts[$i]++;
            }
        }

        return $distributedAmounts;
    }
}
