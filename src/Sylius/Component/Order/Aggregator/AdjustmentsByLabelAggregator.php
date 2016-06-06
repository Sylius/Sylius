<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Order\Aggregator;

use Sylius\Component\Order\Model\AdjustmentInterface;
use Webmozart\Assert\Assert;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class AdjustmentsByLabelAggregator implements AdjustmentsAggregatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function aggregate(array $adjustments)
    {
        $aggregatedAdjustments = [];
        foreach ($adjustments as $adjustment) {
            Assert::isInstanceOf($adjustment, AdjustmentInterface::class);

            if (!isset($aggregatedAdjustments[$adjustment->getLabel()])) {
                $aggregatedAdjustments[$adjustment->getLabel()] = 0;
            }

            $aggregatedAdjustments[$adjustment->getLabel()] += $adjustment->getAmount();
        }

        return $aggregatedAdjustments;
    }
}
