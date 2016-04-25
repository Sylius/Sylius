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
        foreach ($elements as $element) {
            $distributedAmounts[] = (int) round(($element * $amount) / $total, 0, PHP_ROUND_HALF_DOWN);
        }

        $missingAmount = $amount - array_sum($distributedAmounts);
        for ($i = 0; $i < abs($missingAmount); $i++) {
            $distributedAmounts[$i] += $missingAmount >= 0 ? 1 : -1;
        }

        return $distributedAmounts;
    }
}
