<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ChannelBundle\Context\FakeChannel;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ChannelBundle\Context\FakeChannel\FakeChannelCodeProviderInterface;
use Sylius\Bundle\ChannelBundle\Context\FakeChannel\FakeChannelContext;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class FakeChannelContextSpec extends ObjectBehavior
{
    function let(
        FakeChannelCodeProviderInterface $fakeChannelCodeProvider,
        ChannelRepositoryInterface $channelRepository,
        RequestStack $requestStack
    ) {
        $this->beConstructedWith($fakeChannelCodeProvider, $channelRepository, $requestStack);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(FakeChannelContext::class);
    }

    function it_implements_channel_context_interface()
    {
        $this->shouldImplement(ChannelContextInterface::class);
    }

    function it_returns_a_fake_channel_with_the_given_code(
        FakeChannelCodeProviderInterface $fakeChannelCodeProvider,
        ChannelRepositoryInterface $channelRepository,
        RequestStack $requestStack,
        Request $request,
        ChannelInterface $channel
    ) {
        $requestStack->getMasterRequest()->willReturn($request);

        $fakeChannelCodeProvider->getCode($request)->willReturn('CHANNEL_CODE');

        $channelRepository->findOneByCode('CHANNEL_CODE')->willReturn($channel);

        $this->getChannel()->shouldReturn($channel);
    }

    function it_throws_a_channel_not_found_exception_if_there_is_no_master_request(RequestStack $requestStack)
    {
        $requestStack->getMasterRequest()->willReturn(null);

        $this->shouldThrow(ChannelNotFoundException::class)->during('getChannel');
    }

    function it_throws_a_channel_not_found_exception_if_channel_with_given_code_was_not_found(
        FakeChannelCodeProviderInterface $fakeChannelCodeProvider,
        ChannelRepositoryInterface $channelRepository,
        RequestStack $requestStack,
        Request $request
    ) {
        $requestStack->getMasterRequest()->willReturn($request);

        $fakeChannelCodeProvider->getCode($request)->willReturn('CHANNEL_CODE');

        $channelRepository->findOneByCode('CHANNEL_CODE')->willReturn(null);

        $this->shouldThrow(ChannelNotFoundException::class)->during('getChannel');
    }
}
