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

use Pamil\ProphecyCommon\Promise\CompositePromise;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\CachedPerRequestChannelContext;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class CachedPerRequestChannelContextSpec extends ObjectBehavior
{
    function let(ChannelContextInterface $decoratedChannelContext, RequestStack $requestStack)
    {
        $this->beConstructedWith($decoratedChannelContext, $requestStack);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CachedPerRequestChannelContext::class);
    }

    function it_implements_channel_context_interface()
    {
        $this->shouldImplement(ChannelContextInterface::class);
    }

    function it_caches_channels_for_the_same_request(
        ChannelContextInterface $decoratedChannelContext,
        RequestStack $requestStack,
        Request $request,
        ChannelInterface $channel
    ) {
        $requestStack->getMasterRequest()->willReturn($request, $request);

        $decoratedChannelContext->getChannel()->willReturn($channel)->shouldBeCalledTimes(1);

        $this->getChannel()->shouldReturn($channel);
        $this->getChannel()->shouldReturn($channel);
    }

    function it_does_not_cache_channels_for_different_requests(
        ChannelContextInterface $decoratedChannelContext,
        RequestStack $requestStack,
        Request $firstRequest,
        Request $secondRequest,
        ChannelInterface $firstChannel,
        ChannelInterface $secondChannel
    ) {
        $requestStack->getMasterRequest()->willReturn($firstRequest, $secondRequest);

        $decoratedChannelContext->getChannel()->willReturn($firstChannel, $secondChannel);

        $this->getChannel()->shouldReturn($firstChannel);
        $this->getChannel()->shouldReturn($secondChannel);
    }

    function it_caches_channels_for_the_same_request_even_if_there_are_other_request_in_between(
        ChannelContextInterface $decoratedChannelContext,
        RequestStack $requestStack,
        Request $firstRequest,
        Request $secondRequest,
        ChannelInterface $firstChannel,
        ChannelInterface $secondChannel
    ) {
        $requestStack->getMasterRequest()->willReturn($firstRequest, $secondRequest, $firstRequest);

        $decoratedChannelContext->getChannel()->willReturn($firstChannel, $secondChannel)->shouldBeCalledTimes(2);

        $this->getChannel()->shouldReturn($firstChannel);
        $this->getChannel()->shouldReturn($secondChannel);
        $this->getChannel()->shouldReturn($firstChannel);
    }

    function it_does_not_cache_results_while_there_are_no_master_requests(
        ChannelContextInterface $decoratedChannelContext,
        RequestStack $requestStack,
        ChannelInterface $firstChannel,
        ChannelInterface $secondChannel
    ) {
        $requestStack->getMasterRequest()->willReturn(null, null);

        $decoratedChannelContext->getChannel()->willReturn($firstChannel, $secondChannel)->shouldBeCalledTimes(2);

        $this->getChannel()->shouldReturn($firstChannel);
        $this->getChannel()->shouldReturn($secondChannel);
    }

    function it_caches_channel_not_found_exceptions_for_the_same_request(
        ChannelContextInterface $decoratedChannelContext,
        RequestStack $requestStack,
        Request $request
    ) {
        $requestStack->getMasterRequest()->willReturn($request, $request);

        $decoratedChannelContext->getChannel()->willThrow(ChannelNotFoundException::class)->shouldBeCalledTimes(1);

        $this->shouldThrow(ChannelNotFoundException::class)->during('getChannel');
        $this->shouldThrow(ChannelNotFoundException::class)->during('getChannel');
    }

    function it_does_not_cache_channel_not_found_exceptions_for_null_master_requests(
        ChannelContextInterface $decoratedChannelContext,
        RequestStack $requestStack,
        ChannelInterface $channel
    ) {
        $requestStack->getMasterRequest()->willReturn(null, null);

        $decoratedChannelContext
            ->getChannel()
            ->will(CompositePromise::it()->willThrow(ChannelNotFoundException::class)->andThenReturn($channel))
            ->shouldBeCalledTimes(2)
        ;

        $this->shouldThrow(ChannelNotFoundException::class)->during('getChannel');
        $this->getChannel()->shouldReturn($channel);
    }
}
