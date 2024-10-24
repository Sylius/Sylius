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

namespace spec\Sylius\Bundle\ApiBundle\StateProcessor\Admin\Channel;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\State\ProcessorInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Exception\ChannelCannotBeRemoved;
use Sylius\Component\Channel\Checker\ChannelDeletionCheckerInterface;
use Sylius\Component\Core\Model\ChannelInterface;

final class RemoveProcessorSpec extends ObjectBehavior
{
    function let(
        ProcessorInterface $removeProcessor,
        ChannelDeletionCheckerInterface $channelDeletionChecker,
    ): void {
        $this->beConstructedWith($removeProcessor, $channelDeletionChecker);
    }

    function it_throws_an_exception_if_object_is_not_a_channel(
        ProcessorInterface $removeProcessor,
        ChannelDeletionCheckerInterface $channelDeletionChecker,
    ): void {
        $channelDeletionChecker->isDeletable(Argument::any())->shouldNotBeCalled();
        $removeProcessor->process(Argument::cetera())->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('process', [new \stdClass(), new Delete(), [], []])
        ;
    }

    function it_throws_exception_if_channel_is_not_deletable(
        ProcessorInterface $removeProcessor,
        ChannelDeletionCheckerInterface $channelDeletionChecker,
        ChannelInterface $channel,
    ): void {
        $uriVariables = [];
        $context = [];

        $channelDeletionChecker->isDeletable($channel)->willReturn(false);

        $removeProcessor->process(Argument::cetera())->shouldNotBeCalled();

        $this
            ->shouldThrow(ChannelCannotBeRemoved::class)
            ->during('process', [$channel, new Delete(), $uriVariables, $context])
        ;
    }

    function it_uses_decorated_data_persister_to_remove_channel(
        ProcessorInterface $removeProcessor,
        ChannelDeletionCheckerInterface $channelDeletionChecker,
        ChannelInterface $channel,
    ): void {
        $operation = new Delete();
        $uriVariables = [];
        $context = [];

        $channelDeletionChecker->isDeletable($channel)->willReturn(true);

        $removeProcessor->process($channel, $operation, $uriVariables, $context)->willReturn($channel);

        $this->process($channel, $operation, $uriVariables, $context);
    }
}
