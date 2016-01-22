<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Distributor;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class IntegerDistributor implements IntegerDistributorInterface
{
    /**
     * {@inheritdoc}
     */
    public function distribute($numberOfTargets, $baseAmount)
    {
        if (!is_int($numberOfTargets) || 1 > $numberOfTargets) {
            throw new \InvalidArgumentException('Number of targets must be an integer, bigger than 0.');
        }

        $sign = $baseAmount < 0 ? -1 : 1;
        $amount = abs($baseAmount);

        $low = intval($amount / $numberOfTargets);
        $high = $low + 1;

        $remainder = $amount % $numberOfTargets;
        $result = array();

        for ($i = 0; $i < $remainder; $i++) {
            $result[] = $high * $sign;
        }

        for ($i = $remainder; $i < $numberOfTargets; $i++) {
            $result[] = $low * $sign;
        }

        return $result;
    }
}
