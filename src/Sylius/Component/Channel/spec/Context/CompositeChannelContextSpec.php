<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Channel\Context;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Context\CompositeChannelContext;
use Sylius\Component\Channel\Model\ChannelInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class CompositeChannelContextSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(CompositeChannelContext::class);
    }

    function it_implements_channel_context_interface()
    {
        $this->shouldImplement(ChannelContextInterface::class);
    }

    function it_throws_a_channel_not_found_exception_if_there_are_no_nested_channel_contexts_defined()
    {
        $this->shouldThrow(ChannelNotFoundException::class)->during('getChannel');
    }

    function it_throws_a_channel_not_found_exception_if_none_of_nested_channel_contexts_returned_a_channel(
        ChannelContextInterface $channelContext
    ) {
        $channelContext->getChannel()->willThrow(ChannelNotFoundException::class);

        $this->addContext($channelContext);

        $this->shouldThrow(ChannelNotFoundException::class)->during('getChannel');
    }

    function it_returns_first_result_returned_by_nested_request_resolvers(
        ChannelContextInterface $firstChannelContext,
        ChannelContextInterface $secondChannelContext,
        ChannelContextInterface $thirdChannelContext,
        ChannelInterface $channel
    ) {
        $firstChannelContext->getChannel()->willThrow(ChannelNotFoundException::class);
        $secondChannelContext->getChannel()->willReturn($channel);
        $thirdChannelContext->getChannel()->shouldNotBeCalled();

        $this->addContext($firstChannelContext);
        $this->addContext($secondChannelContext);
        $this->addContext($thirdChannelContext);

        $this->getChannel()->shouldReturn($channel);
    }

    function its_nested_request_resolvers_can_have_priority(
        ChannelContextInterface $firstChannelContext,
        ChannelContextInterface $secondChannelContext,
        ChannelContextInterface $thirdChannelContext,
        ChannelInterface $channel
    ) {
        $firstChannelContext->getChannel()->shouldNotBeCalled();
        $secondChannelContext->getChannel()->willReturn($channel);
        $thirdChannelContext->getChannel()->willThrow(ChannelNotFoundException::class);

        $this->addContext($firstChannelContext, -5);
        $this->addContext($secondChannelContext, 0);
        $this->addContext($thirdChannelContext, 5);

        $this->getChannel()->shouldReturn($channel);
    }
}
