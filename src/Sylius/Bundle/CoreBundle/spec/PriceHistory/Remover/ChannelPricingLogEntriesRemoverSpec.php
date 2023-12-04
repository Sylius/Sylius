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

namespace spec\Sylius\Bundle\CoreBundle\PriceHistory\Remover;

use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\PriceHistory\Event\OldChannelPricingLogEntriesEvents;
use Sylius\Bundle\CoreBundle\PriceHistory\Remover\ChannelPricingLogEntriesRemover;
use Sylius\Calendar\Provider\DateTimeProviderInterface;
use Sylius\Component\Core\Model\ChannelPricingLogEntryInterface;
use Sylius\Component\Core\Repository\ChannelPricingLogEntryRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class ChannelPricingLogEntriesRemoverSpec extends ObjectBehavior
{
    private const BATCH_SIZE = 1;

    function let(
        ChannelPricingLogEntryRepositoryInterface $channelPricingLogEntriesRepository,
        ObjectManager $manager,
        DateTimeProviderInterface $dateTimeProvider,
        EventDispatcherInterface $eventDispatcher,
    ): void {
        $this->beConstructedWith(
            $channelPricingLogEntriesRepository,
            $manager,
            $dateTimeProvider,
            $eventDispatcher,
            self::BATCH_SIZE,
        );
    }

    function it_implements_channel_pricing_log_entries_remover_interface(): void
    {
        $this->shouldImplement(ChannelPricingLogEntriesRemover::class);
    }

    function it_does_nothing_when_no_log_entries_were_found(
        ChannelPricingLogEntryRepositoryInterface $channelPricingLogEntriesRepository,
        ObjectManager $manager,
        DateTimeProviderInterface $dateTimeProvider,
        EventDispatcherInterface $eventDispatcher,
    ): void {
        $date = new \DateTimeImmutable();
        $dateTimeProvider->now()->willReturn($date);

        $channelPricingLogEntriesRepository->findOlderThan($date->modify('-1 days'), self::BATCH_SIZE)->willReturn([]);

        $manager->remove(Argument::any())->shouldNotBeCalled();
        $manager->flush()->shouldNotBeCalled();
        $manager->clear()->shouldNotBeCalled();

        $eventDispatcher->dispatch(Argument::cetera())->shouldNotBeCalled();

        $this->remove(1);
    }

    function it_removes_a_single_batch_of_channel_pricing_log_entries_when_there_is_no_more(
        ChannelPricingLogEntryRepositoryInterface $channelPricingLogEntriesRepository,
        ObjectManager $manager,
        DateTimeProviderInterface $dateTimeProvider,
        EventDispatcherInterface $eventDispatcher,
        ChannelPricingLogEntryInterface $channelPricingLogEntry,
    ): void {
        $date = new \DateTimeImmutable();
        $dateTimeProvider->now()->willReturn($date);

        $channelPricingLogEntriesRepository
            ->findOlderThan($date->modify('-1 days'), self::BATCH_SIZE)
            ->willReturn([$channelPricingLogEntry], [])
        ;

        $manager->remove($channelPricingLogEntry)->shouldBeCalled();

        $eventDispatcher->dispatch(
            new GenericEvent([$channelPricingLogEntry->getWrappedObject()]),
            OldChannelPricingLogEntriesEvents::PRE_REMOVE,
        )->shouldBeCalled();
        $manager->flush()->shouldBeCalled();

        $eventDispatcher->dispatch(
            new GenericEvent([$channelPricingLogEntry->getWrappedObject()]),
            OldChannelPricingLogEntriesEvents::POST_REMOVE,
        )->shouldBeCalled();
        $manager->clear()->shouldBeCalled();

        $this->remove(1);
    }

    function it_removes_multiple_batches_of_channel_pricing_log_entries(
        ChannelPricingLogEntryRepositoryInterface $channelPricingLogEntriesRepository,
        ObjectManager $manager,
        DateTimeProviderInterface $dateTimeProvider,
        EventDispatcherInterface $eventDispatcher,
        ChannelPricingLogEntryInterface $firstChannelPricingLogEntry,
        ChannelPricingLogEntryInterface $secondChannelPricingLogEntry,
    ): void {
        $date = new \DateTimeImmutable();
        $dateTimeProvider->now()->willReturn($date);

        $channelPricingLogEntriesRepository
            ->findOlderThan($date->modify('-1 days'), self::BATCH_SIZE)
            ->willReturn([$firstChannelPricingLogEntry], [$secondChannelPricingLogEntry], [])
        ;

        $eventDispatcher->dispatch(
            new GenericEvent([$firstChannelPricingLogEntry->getWrappedObject()]),
            OldChannelPricingLogEntriesEvents::PRE_REMOVE,
        )->shouldBeCalledTimes(1);
        $eventDispatcher->dispatch(
            new GenericEvent([$secondChannelPricingLogEntry->getWrappedObject()]),
            OldChannelPricingLogEntriesEvents::PRE_REMOVE,
        )->shouldBeCalledTimes(1);

        $manager->remove($firstChannelPricingLogEntry)->shouldBeCalledTimes(1);
        $manager->remove($secondChannelPricingLogEntry)->shouldBeCalledTimes(1);

        $eventDispatcher->dispatch(
            new GenericEvent([$firstChannelPricingLogEntry->getWrappedObject()]),
            OldChannelPricingLogEntriesEvents::POST_REMOVE,
        )->shouldBeCalledTimes(1);
        $eventDispatcher->dispatch(
            new GenericEvent([$secondChannelPricingLogEntry->getWrappedObject()]),
            OldChannelPricingLogEntriesEvents::POST_REMOVE,
        )->shouldBeCalledTimes(1);

        $manager->flush()->shouldBeCalledTimes(2);
        $manager->clear()->shouldBeCalledTimes(2);

        $this->remove(1);
    }
}
