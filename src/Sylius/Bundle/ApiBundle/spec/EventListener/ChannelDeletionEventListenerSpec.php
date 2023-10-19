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

namespace spec\Sylius\Bundle\ApiBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Exception\ChannelCannotBeRemoved;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class ChannelDeletionEventListenerSpec extends ObjectBehavior
{
    function let(ChannelRepositoryInterface $channelRepository)
    {
        $this->beConstructedWith($channelRepository);
    }

    function it_throws_exception_when_trying_to_delete_the_only_enabled_channel(
        Request $request,
        ChannelInterface $channel,
        ChannelRepositoryInterface $channelRepository,
        HttpKernelInterface $kernel,
    ): void {
        $request->getMethod()->willReturn(Request::METHOD_DELETE);

        $event = new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MAIN_REQUEST,
            $channel->getWrappedObject(),
        );

        $channelRepository->findBy(['enabled' => true])->willReturn([$channel]);

        $this->shouldThrow(ChannelCannotBeRemoved::class)->during('protectFromRemovalTheOnlyChannelInStore', [$event]);
    }

    function it_does_nothing_when_there_are_more_than_one_enabled_channels(
        Request $request,
        ChannelInterface $channel,
        ChannelInterface $channel2,
        ChannelRepositoryInterface $channelRepository,
        HttpKernelInterface $kernel,
    ): void {
        $request->getMethod()->willReturn(Request::METHOD_DELETE);

        $event = new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MAIN_REQUEST,
            $channel->getWrappedObject(),
        );

        $channelRepository->findBy(['enabled' => true])->willReturn([$channel, $channel2]);

        $this->shouldNotThrow(ChannelCannotBeRemoved::class)->during('protectFromRemovalTheOnlyChannelInStore', [$event]);
    }

    function it_does_nothing_when_request_method_is_not_delete(
        Request $request,
        ChannelInterface $channel,
        ChannelRepositoryInterface $channelRepository,
        HttpKernelInterface $kernel,
    ): void {
        $request->getMethod()->willReturn(Request::METHOD_GET);

        $event = new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MAIN_REQUEST,
            $channel->getWrappedObject(),
        );

        $channelRepository->findBy(['enabled' => true])->willReturn([$channel]);

        $this->shouldNotThrow(ChannelCannotBeRemoved::class)->during('protectFromRemovalTheOnlyChannelInStore', [$event]);
    }

    function it_does_nothing_when_event_controller_result_is_not_channel(
        Request $request,
        ChannelRepositoryInterface $channelRepository,
        HttpKernelInterface $kernel,
        OrderInterface $order,
    ): void {
        $request->getMethod()->willReturn(Request::METHOD_DELETE);

        $event = new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MAIN_REQUEST,
            $order->getWrappedObject(),
        );

        $channelRepository->findBy(['enabled' => true])->willReturn([]);

        $this->shouldNotThrow(ChannelCannotBeRemoved::class)->during('protectFromRemovalTheOnlyChannelInStore', [$event]);
    }
}
