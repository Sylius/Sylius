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
            $this->assertElementIsAdjustment($adjustment);

            if (!isset($aggregatedAdjustments[$adjustment->getLabel()])) {
                $aggregatedAdjustments[$adjustment->getLabel()] = 0;
            }

            $aggregatedAdjustments[$adjustment->getLabel()] += $adjustment->getAmount();
        }

        return $aggregatedAdjustments;
    }

    /**
     * @param mixed $adjustment
     *
     * @throws \InvalidArgumentException
     */
    private function assertElementIsAdjustment($adjustment)
    {
        if (!$adjustment instanceof AdjustmentInterface) {
            throw new \InvalidArgumentException('Each adjustments array element must implement '.AdjustmentInterface::class.'.');
        }
    }
}
