<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Checker;

trait CountComparisonTrait
{
    /**
     * @param int    $count
     * @param int    $requiredCount
     * @param string $comparison
     *
     * @return bool
     */
    public static function comparison($count, $requiredCount, $comparison = 'equal_or_more')
    {
        if ('less_than' === $comparison) {
            return $count < $requiredCount;
        }

        if ('more_than' === $comparison) {
            return $count > $requiredCount;
        }

        if ('equal_or_more' === $comparison || 'equal' === $comparison) {
            return $count >= $requiredCount;
        }

        if ('exactly' === $comparison) {
            return $count === $requiredCount;
        }

        if ('modulo' === $comparison) {
            return 0 === $count % $requiredCount;
        }

        return false;
    }
}
