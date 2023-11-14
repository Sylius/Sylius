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

namespace spec\Sylius\Bundle\ApiBundle\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Exception\ChannelCannotBeRemoved;
use Sylius\Component\Channel\Checker\ChannelDeletionCheckerInterface;
use Sylius\Component\Core\Model\ChannelInterface;

final class ChannelDataPersisterSpec extends ObjectBehavior
{
    function let(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        ChannelDeletionCheckerInterface $channelDeletionChecker,
    ): void {
        $this->beConstructedWith($decoratedDataPersister, $channelDeletionChecker);
    }

    function it_supports_only_channel_entity(ChannelInterface $channel, \stdClass $object): void
    {
        $this->supports($channel)->shouldReturn(true);
        $this->supports($object)->shouldReturn(false);
    }

    function it_persists_channel(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        ChannelInterface $channel,
    ): void {
        $decoratedDataPersister->persist($channel, [])->willReturn($channel);

        $this->persist($channel, [])->shouldReturn($channel);
    }

    function it_throws_an_exception_if_channel_is_not_deletable(
        ChannelDeletionCheckerInterface $channelDeletionChecker,
        ChannelInterface $channel,
    ): void {
        $channelDeletionChecker->isDeletable($channel)->willReturn(false);

        $this
            ->shouldThrow(ChannelCannotBeRemoved::class)
            ->during('remove', [$channel])
        ;
    }

    function it_removes_channel(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        ChannelDeletionCheckerInterface $channelDeletionChecker,
        ChannelInterface $channel,
    ): void {
        $channelDeletionChecker->isDeletable($channel)->willReturn(true);
        $decoratedDataPersister->remove($channel, [])->willReturn(null);

        $this->remove($channel, [])->shouldReturn(null);
    }
}
