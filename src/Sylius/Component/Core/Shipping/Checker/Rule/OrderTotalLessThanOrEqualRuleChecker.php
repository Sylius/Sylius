<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Shipping\Checker\Rule;

final class OrderTotalLessThanOrEqualRuleChecker extends OrderTotalRuleChecker
{
    public const TYPE = 'order_total_less_than_or_equal';

    protected function compare(int $total, int $threshold): bool
    {
        return $total <= $threshold;
    }
}
