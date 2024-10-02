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

use Sylius\Bundle\OrderBundle\Templating\Helper\AdjustmentsHelper;
use Sylius\Component\Order\Aggregator\AdjustmentsAggregatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class AggregateAdjustmentsExtension extends AbstractExtension
{
    public function __construct(private AdjustmentsHelper|AdjustmentsAggregatorInterface $adjustmentsHelper)
    {
        if ($this->adjustmentsHelper instanceof AdjustmentsHelper) {
            trigger_deprecation(
                'sylius/order-bundle',
                '1.14',
                'Passing an instance of %s as constructor argument for %s is deprecated and will be prohibited in Sylius 2.0. Pass an instance of %s instead.',
                AdjustmentsHelper::class,
                self::class,
                AdjustmentsAggregatorInterface::class,
            );

            trigger_deprecation(
                'sylius/order-bundle',
                '1.14',
                'The argument name $adjustmentsHelper is deprecated and will be renamed to $adjustmentsAggregator in Sylius 2.0.',
            );
        }
    }

    public function getFunctions(): array
    {
        if ($this->adjustmentsHelper instanceof AdjustmentsAggregatorInterface) {
            return [
                new TwigFunction('sylius_aggregate_adjustments', [$this->adjustmentsHelper, 'aggregate']),
            ];
        }

        return [
            new TwigFunction('sylius_aggregate_adjustments', [$this->adjustmentsHelper, 'getAggregatedAdjustments']),
        ];
    }
}
