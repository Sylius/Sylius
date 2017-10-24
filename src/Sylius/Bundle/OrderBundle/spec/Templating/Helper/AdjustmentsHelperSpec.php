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

namespace spec\Sylius\Bundle\OrderBundle\Templating\Helper;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Order\Aggregator\AdjustmentsAggregatorInterface;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Symfony\Component\Templating\Helper\Helper;

final class AdjustmentsHelperSpec extends ObjectBehavior
{
    function let(AdjustmentsAggregatorInterface $adjustmentsAggregator): void
    {
        $this->beConstructedWith($adjustmentsAggregator);
    }

    function it_is_a_helper(): void
    {
        $this->shouldHaveType(Helper::class);
    }

    function it_returns_aggregated_adjustments(
        AdjustmentsAggregatorInterface $adjustmentsAggregator,
        AdjustmentInterface $adjustment1,
        AdjustmentInterface $adjustment2,
        AdjustmentInterface $adjustment3
    ): void {
        $adjustmentsAggregator
            ->aggregate([$adjustment1, $adjustment2, $adjustment3])
            ->willReturn(['tax 1' => 1000, 'tax2' => 500])
        ;

        $this
            ->getAggregatedAdjustments([$adjustment1, $adjustment2, $adjustment3])
            ->shouldReturn(['tax 1' => 1000, 'tax2' => 500])
        ;
    }

    function it_has_a_name(): void
    {
        $this->getName()->shouldReturn('sylius_adjustments');
    }
}
