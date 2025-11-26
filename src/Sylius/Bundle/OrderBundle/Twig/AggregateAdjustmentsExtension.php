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

namespace Sylius\Bundle\OrderBundle\Twig;

use Sylius\Component\Order\Aggregator\AdjustmentsAggregatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class AggregateAdjustmentsExtension extends AbstractExtension
{
    public function __construct(private AdjustmentsAggregatorInterface $adjustmentsAggregator)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_aggregate_adjustments', [$this->adjustmentsAggregator, 'aggregate']),
        ];
    }
}
