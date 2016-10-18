<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Channel\Context\RequestBased;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Context\RequestBased\ChannelContext;
use Sylius\Component\Channel\Context\RequestBased\RequestResolverInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ChannelContextSpec extends ObjectBehavior
{
    function let(RequestResolverInterface $requestResolver, RequestStack $requestStack)
    {
        $this->beConstructedWith($requestResolver, $requestStack);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ChannelContext::class);
    }

    function it_implements_channel_context_interface()
    {
        $this->shouldImplement(ChannelContextInterface::class);
    }

    function it_proxies_master_request_to_request_resolver(
        RequestResolverInterface $requestResolver,
        RequestStack $requestStack,
        Request $masterRequest,
        ChannelInterface $channel
    ) {
        $requestStack->getMasterRequest()->willReturn($masterRequest);

        $requestResolver->findChannel($masterRequest)->willReturn($channel);

        $this->getChannel()->shouldReturn($channel);
    }

    function it_throws_a_channel_not_found_exception_if_request_resolver_returns_null(
        RequestResolverInterface $requestResolver,
        RequestStack $requestStack,
        Request $masterRequest
    ) {
        $requestStack->getMasterRequest()->willReturn($masterRequest);

        $requestResolver->findChannel($masterRequest)->willReturn(null);

        $this->shouldThrow(ChannelNotFoundException::class)->during('getChannel');
    }

    function it_throws_a_channel_not_found_exception_if_there_is_no_master_request(
        RequestStack $requestStack
    ) {
        $requestStack->getMasterRequest()->willReturn(null);

        $this->shouldThrow(ChannelNotFoundException::class)->during('getChannel');
    }
}
