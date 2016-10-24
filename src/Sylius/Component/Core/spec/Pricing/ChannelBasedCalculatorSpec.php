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
use Sylius\Component\Core\Pricing\ChannelBasedCalculator;
use Sylius\Component\Pricing\Calculator\CalculatorInterface;
use Sylius\Component\Pricing\Model\PriceableInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ChannelBasedCalculatorSpec extends ObjectBehavior
{
    function let(ChannelContextInterface $channelContext)
    {
        $this->beConstructedWith($channelContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ChannelBasedCalculator::class);
    }

    function it_implements_a_calculator_interface()
    {
        $this->shouldImplement(CalculatorInterface::class);
    }

    function it_calculates_a_subject_price_based_on_current_channel(
        $channelContext,
        ChannelInterface $channel,
        PriceableInterface $subject
    ) {
        $channelContext->getChannel()->willReturn($channel);
        $channel->getId()->willReturn(1);

        $subject->getPricingConfiguration()->willReturn([1 => 1400]);

        $this->calculate($subject, [], [])->shouldReturn(1400);
    }

    function it_returns_a_default_price_if_current_channel_price_is_not_configured(
        $channelContext,
        ChannelInterface $channel,
        PriceableInterface $subject
    ) {
        $channelContext->getChannel()->willReturn($channel);
        $channel->getId()->willReturn(1);

        $subject->getPricingConfiguration()->willReturn([2 => 1400]);
        $subject->getPrice()->willReturn(2000);

        $this->calculate($subject, [], [])->shouldReturn(2000);
    }

    function it_has_a_type()
    {
        $this->getType()->shouldReturn('channel_based');
    }
}
