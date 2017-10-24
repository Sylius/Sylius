<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Order\Aggregator;

use Sylius\Component\Order\Model\AdjustmentInterface;

interface AdjustmentsAggregatorInterface
{
    /**
     * @param iterable|AdjustmentInterface[] $adjustments
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function aggregate(iterable $adjustments): array;
}
