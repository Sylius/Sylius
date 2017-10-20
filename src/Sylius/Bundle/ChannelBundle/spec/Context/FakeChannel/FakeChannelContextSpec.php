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

namespace spec\Sylius\Bundle\ChannelBundle\Context\FakeChannel;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ChannelBundle\Context\FakeChannel\FakeChannelCodeProviderInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class FakeChannelContextSpec extends ObjectBehavior
{
    function let(
        FakeChannelCodeProviderInterface $fakeChannelCodeProvider,
        ChannelRepositoryInterface $channelRepository,
        RequestStack $requestStack
    ): void {
        $this->beConstructedWith($fakeChannelCodeProvider, $channelRepository, $requestStack);
    }

    function it_implements_channel_context_interface(): void
    {
        $this->shouldImplement(ChannelContextInterface::class);
    }

    function it_returns_a_fake_channel_with_the_given_code(
        FakeChannelCodeProviderInterface $fakeChannelCodeProvider,
        ChannelRepositoryInterface $channelRepository,
        RequestStack $requestStack,
        Request $request,
        ChannelInterface $channel
    ): void {
        $requestStack->getMasterRequest()->willReturn($request);

        $fakeChannelCodeProvider->getCode($request)->willReturn('CHANNEL_CODE');

        $channelRepository->findOneByCode('CHANNEL_CODE')->willReturn($channel);

        $this->getChannel()->shouldReturn($channel);
    }

    function it_throws_a_channel_not_found_exception_if_there_is_no_master_request(RequestStack $requestStack): void
    {
        $requestStack->getMasterRequest()->willReturn(null);

        $this->shouldThrow(ChannelNotFoundException::class)->during('getChannel');
    }

    function it_throws_a_channel_not_found_exception_if_provided_code_was_null(
        FakeChannelCodeProviderInterface $fakeChannelCodeProvider,
        ChannelRepositoryInterface $channelRepository,
        RequestStack $requestStack,
        Request $request
    ): void {
        $requestStack->getMasterRequest()->willReturn($request);

        $fakeChannelCodeProvider->getCode($request)->willReturn(null);

        $channelRepository->findOneByCode(Argument::any())->shouldNotBeCalled();

        $this->shouldThrow(ChannelNotFoundException::class)->during('getChannel');
    }

    function it_throws_a_channel_not_found_exception_if_channel_with_given_code_was_not_found(
        FakeChannelCodeProviderInterface $fakeChannelCodeProvider,
        ChannelRepositoryInterface $channelRepository,
        RequestStack $requestStack,
        Request $request
    ): void {
        $requestStack->getMasterRequest()->willReturn($request);

        $fakeChannelCodeProvider->getCode($request)->willReturn('CHANNEL_CODE');

        $channelRepository->findOneByCode('CHANNEL_CODE')->willReturn(null);

        $this->shouldThrow(ChannelNotFoundException::class)->during('getChannel');
    }
}
