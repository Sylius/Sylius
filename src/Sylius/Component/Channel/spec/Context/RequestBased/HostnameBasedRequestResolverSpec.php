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

namespace spec\Sylius\Component\Channel\Context\RequestBased;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\RequestBased\RequestResolverInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

final class HostnameBasedRequestResolverSpec extends ObjectBehavior
{
    function let(ChannelRepositoryInterface $channelRepository): void
    {
        $this->beConstructedWith($channelRepository);
    }

    function it_implements_request_resolver_interface(): void
    {
        $this->shouldImplement(RequestResolverInterface::class);
    }

    function it_finds_the_channel_by_request_hostname(
        ChannelRepositoryInterface $channelRepository,
        Request $request,
        ChannelInterface $channel
    ): void {
        $request->getHost()->willReturn('example.org');

        $channelRepository->findOneByHostname('example.org')->willReturn($channel);

        $this->findChannel($request)->shouldReturn($channel);
    }

    function it_returns_null_if_channel_was_not_found(
        ChannelRepositoryInterface $channelRepository,
        Request $request
    ): void {
        $request->getHost()->willReturn('example.org');

        $channelRepository->findOneByHostname('example.org')->willReturn(null);

        $this->findChannel($request)->shouldReturn(null);
    }
}
