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
class TaxesDistributor implements TaxesDistributorInterface
{
    /**
     * {@inheritdoc}
     */
    public function distribute($taxItems, $taxAmount)
    {
        if (!is_int($taxItems) || 1 > $taxItems) {
            throw new \InvalidArgumentException('Tax items number must be an integer, bigger than 0.');
        }

        $sign = ($taxAmount < 0) ? -1 : 1;
        $amount = abs($taxAmount);

        $low = intval($amount / $taxItems);
        $high = $low + 1;

        $remainder = $amount % $taxItems;
        $result = array();

        for ($i = 0; $i < $remainder; $i++) {
            $result[] = $high * $sign;
        }

        for ($i = $remainder; $i < $taxItems; $i++) {
            $result[] = $low * $sign;
        }

        return $result;
    }
}
