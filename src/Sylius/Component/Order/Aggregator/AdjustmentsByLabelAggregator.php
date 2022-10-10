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

namespace Sylius\Component\Order\Aggregator;

use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\AdjustmentInterface as BaseAdjustmentInterface;
use Webmozart\Assert\Assert;

final class AdjustmentsByLabelAggregator implements AdjustmentsAggregatorInterface
{
    public function aggregate(iterable $adjustments): array
    {
        $aggregatedAdjustments = [];
        foreach ($adjustments as $adjustment) {
            Assert::isInstanceOf($adjustment, AdjustmentInterface::class);

            if (!isset($aggregatedAdjustments[$adjustment->getLabel()])) {
                $aggregatedAdjustments[$adjustment->getLabel()] = 0;
            }

//            if ($adjustment->getType() === BaseAdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT) {
//
//            }

            $aggregatedAdjustments[$adjustment->getLabel()] += $adjustment->getAmount();
        }

        return $aggregatedAdjustments;
    }
}
