<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\OrderBundle\Templating\Helper;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\OrderBundle\Templating\Helper\AdjustmentsHelper;
use Sylius\Component\Order\Aggregator\AdjustmentsAggregatorInterface;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class AdjustmentsHelperSpec extends ObjectBehavior
{
    function let(AdjustmentsAggregatorInterface $adjustmentsAggregator)
    {
        $this->beConstructedWith($adjustmentsAggregator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AdjustmentsHelper::class);
    }

    function it_is_a_helper()
    {
        $this->shouldHaveType(Helper::class);
    }

    function it_returns_aggregated_adjustments(
        AdjustmentsAggregatorInterface $adjustmentsAggregator,
        AdjustmentInterface $adjustment1,
        AdjustmentInterface $adjustment2,
        AdjustmentInterface $adjustment3
    ) {
        $adjustmentsAggregator
            ->aggregate([$adjustment1, $adjustment2, $adjustment3])
            ->willReturn(['tax 1' => 1000, 'tax2' => 500])
        ;

        $this
            ->getAggregatedAdjustments([$adjustment1, $adjustment2, $adjustment3])
            ->shouldReturn(['tax 1' => 1000, 'tax2' => 500])
        ;
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_adjustments');
    }
}
