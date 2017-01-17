<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Order\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Order\Factory\AdjustmentFactory;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class AdjustmentFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $adjustmentFactory)
    {
        $this->beConstructedWith($adjustmentFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AdjustmentFactory::class);
    }

    function it_implements_an_adjustment_factory_interface()
    {
        $this->shouldImplement(AdjustmentFactoryInterface::class);
    }

    function it_creates_new_adjustment(
        FactoryInterface $adjustmentFactory,
        AdjustmentInterface $adjustment
    ) {
        $adjustmentFactory->createNew()->willReturn($adjustment);

        $this->createNew()->shouldReturn($adjustment);
    }

    function it_creates_new_adjustment_with_provided_data(
        FactoryInterface $adjustmentFactory,
        AdjustmentInterface $adjustment
    ) {
        $adjustmentFactory->createNew()->willReturn($adjustment);
        $adjustment->setType('tax')->shouldBeCalled();
        $adjustment->setLabel('Tax description')->shouldBeCalled();
        $adjustment->setAmount(1000)->shouldBeCalled();
        $adjustment->setNeutral(false)->shouldBeCalled();

        $this->createWithData('tax', 'Tax description', 1000, false)->shouldReturn($adjustment);
    }
}
