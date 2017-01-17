<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Order\Aggregator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Order\Aggregator\AdjustmentsAggregatorInterface;
use Sylius\Component\Order\Aggregator\AdjustmentsByLabelAggregator;
use Sylius\Component\Order\Model\AdjustmentInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class AdjustmentsByLabelAggregatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(AdjustmentsByLabelAggregator::class);
    }

    function it_implements_adjustments_aggregator_interface()
    {
        $this->shouldImplement(AdjustmentsAggregatorInterface::class);
    }

    function it_aggregates_given_adjustments_array_by_description(
        AdjustmentInterface $adjustment1,
        AdjustmentInterface $adjustment2,
        AdjustmentInterface $adjustment3,
        AdjustmentInterface $adjustment4
    ) {
        $adjustment1->getLabel()->willReturn('tax 1');
        $adjustment1->getAmount()->willReturn(1000);
        $adjustment2->getLabel()->willReturn('tax 1');
        $adjustment2->getAmount()->willReturn(3000);
        $adjustment3->getLabel()->willReturn('tax 2');
        $adjustment3->getAmount()->willReturn(4000);
        $adjustment4->getLabel()->willReturn('tax 2');
        $adjustment4->getAmount()->willReturn(-2000);

        $this->aggregate([$adjustment1, $adjustment2, $adjustment3, $adjustment4])->shouldReturn([
            'tax 1' => 4000,
            'tax 2' => 2000,
        ]);
    }

    function it_throws_exception_if_any_array_element_is_not_adjustment(
        AdjustmentInterface $adjustment1,
        AdjustmentInterface $adjustment2
    ) {
        $adjustment1->getLabel()->willReturn('tax 1');
        $adjustment1->getAmount()->willReturn(1000);
        $adjustment2->getLabel()->willReturn('tax 1');
        $adjustment2->getAmount()->willReturn(3000);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('aggregate', [[$adjustment1, $adjustment2, 'badObject']])
        ;
    }
}
