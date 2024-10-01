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

namespace Sylius\Bundle\OrderBundle\Templating\Helper;

use Sylius\Component\Order\Aggregator\AdjustmentsAggregatorInterface;
use Sylius\Component\Order\Aggregator\AdjustmentsByLabelAggregator;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Symfony\Component\Templating\Helper\Helper;

trigger_deprecation(
    'sylius/order-bundle',
    '1.14',
    'The "%s" class is deprecated, use "%s" instead.',
    AdjustmentsHelper::class,
    AdjustmentsByLabelAggregator::class,
);

/** @deprecated since Sylius 1.14 and will be removed in Sylius 2.0. Use {@see \Sylius\Component\Order\Aggregator\AdjustmentsAggregatorInterface} instead. */
class AdjustmentsHelper extends Helper
{
    public function __construct(private AdjustmentsAggregatorInterface $adjustmentsAggregator)
    {
    }

    /**
     * @param iterable|AdjustmentInterface[] $adjustments
     */
    public function getAggregatedAdjustments(iterable $adjustments): array
    {
        return $this->adjustmentsAggregator->aggregate($adjustments);
    }

    public function getName(): string
    {
        return 'sylius_adjustments';
    }
}
