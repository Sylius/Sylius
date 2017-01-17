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
interface AdjustmentsAggregatorInterface
{
    /**
     * @param AdjustmentInterface[] $adjustments
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function aggregate(array $adjustments);
}
