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
class IntegerDistributor implements IntegerDistributorInterface
{
    /**
     * {@inheritdoc}
     */
    public function distribute($amount, $numberOfTargets)
    {
        if (!$this->validateNumberOfTargets($numberOfTargets)) {
            throw new \InvalidArgumentException('Number of targets must be an integer, bigger than 0.');
        }

        $sign = $amount < 0 ? -1 : 1;
        $amount = abs($amount);

        $low = (int) ($amount / $numberOfTargets);
        $high = $low + 1;

        $remainder = $amount % $numberOfTargets;
        $result = [];

        for ($i = 0; $i < $remainder; ++$i) {
            $result[] = $high * $sign;
        }

        for ($i = $remainder; $i < $numberOfTargets; ++$i) {
            $result[] = $low * $sign;
        }

        return $result;
    }

    /**
     * @param int $numberOfTargets
     *
     * @return bool
     */
    private function validateNumberOfTargets($numberOfTargets)
    {
        return is_int($numberOfTargets) && 1 <= $numberOfTargets;
    }
}
