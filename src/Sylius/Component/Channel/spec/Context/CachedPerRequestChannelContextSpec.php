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

namespace spec\Sylius\Component\Channel\Context;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class CachedPerRequestChannelContextSpec extends ObjectBehavior
{
    function let(ChannelContextInterface $decoratedChannelContext, RequestStack $requestStack): void
    {
        $this->beConstructedWith($decoratedChannelContext, $requestStack);
    }

    function it_implements_channel_context_interface(): void
    {
        $this->shouldImplement(ChannelContextInterface::class);
    }

    function it_caches_channels_for_the_same_request(
        ChannelContextInterface $decoratedChannelContext,
        RequestStack $requestStack,
        Request $request,
        ChannelInterface $channel,
    ): void {
        $requestStack->getMainRequest()->willReturn($request, $request);

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
        ChannelInterface $secondChannel,
    ): void {
        $requestStack->getMainRequest()->willReturn($firstRequest, $secondRequest);

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
        ChannelInterface $secondChannel,
    ): void {
        $requestStack->getMainRequest()->willReturn($firstRequest, $secondRequest, $firstRequest);

        $decoratedChannelContext->getChannel()->willReturn($firstChannel, $secondChannel)->shouldBeCalledTimes(2);

        $this->getChannel()->shouldReturn($firstChannel);
        $this->getChannel()->shouldReturn($secondChannel);
        $this->getChannel()->shouldReturn($firstChannel);
    }

    function it_does_not_cache_results_while_there_are_no_master_requests(
        ChannelContextInterface $decoratedChannelContext,
        RequestStack $requestStack,
        ChannelInterface $firstChannel,
        ChannelInterface $secondChannel,
    ): void {
        $requestStack->getMainRequest()->willReturn(null, null);

        $decoratedChannelContext->getChannel()->willReturn($firstChannel, $secondChannel)->shouldBeCalledTimes(2);

        $this->getChannel()->shouldReturn($firstChannel);
        $this->getChannel()->shouldReturn($secondChannel);
    }

    function it_caches_channel_not_found_exceptions_for_the_same_request(
        ChannelContextInterface $decoratedChannelContext,
        RequestStack $requestStack,
        Request $request,
    ): void {
        $requestStack->getMainRequest()->willReturn($request, $request);

        $decoratedChannelContext->getChannel()->willThrow(ChannelNotFoundException::class)->shouldBeCalledTimes(1);

        $this->shouldThrow(ChannelNotFoundException::class)->during('getChannel');
        $this->shouldThrow(ChannelNotFoundException::class)->during('getChannel');
    }

    function it_does_not_cache_channel_not_found_exceptions_for_null_master_requests(
        ChannelContextInterface $decoratedChannelContext,
        RequestStack $requestStack,
        ChannelInterface $channel,
    ): void {
        $requestStack->getMainRequest()->willReturn(null, null);

        $decoratedChannelContext
            ->getChannel()
            ->will(new class($channel->getWrappedObject()) {
                private int $counter = 0;

                public function __construct(private ChannelInterface $channel)
                {
                }

                /** @throws ChannelNotFoundException */
                public function __invoke(): ChannelInterface
                {
                    $currentCounter = $this->counter;
                    ++$this->counter;

                    if ($currentCounter === 0) {
                        throw new ChannelNotFoundException();
                    }

                    return $this->channel;
                }
            })
            ->shouldBeCalledTimes(2)
        ;

        $this->shouldThrow(ChannelNotFoundException::class)->during('getChannel');
        $this->getChannel()->shouldReturn($channel);
    }
}
