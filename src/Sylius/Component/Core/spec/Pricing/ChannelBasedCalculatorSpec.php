<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Pricing;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Pricing\Model\PriceableInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ChannelBasedCalculatorSpec extends ObjectBehavior
{
    function let(ChannelContextInterface $channelContext)
    {
        $this->beConstructedWith($channelContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Pricing\ChannelBasedCalculator');
    }

    function it_implements_calculator_interface()
    {
        $this->shouldImplement('Sylius\Component\Pricing\Calculator\CalculatorInterface');
    }

    function it_calculates_subject_price_based_on_current_channel(PriceableInterface $subject, ChannelInterface $channel, $channelContext)
    {
        $channelContext->getChannel()->willReturn($channel)->shouldBeCalled();
        $channel->getId()->willReturn(1)->shouldBeCalled();

        $subject->getPricingConfiguration()->willReturn(array(1 => 1400))->shouldBeCalled();

        $this->calculate($subject, array(), array())->shouldReturn(1400);
    }

    function it_has_type()
    {
        $this->getType()->shouldReturn('channel_based');
    }
}
