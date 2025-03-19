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

namespace Sylius\Bundle\CoreBundle\PriceHistory\Remover;

use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\CoreBundle\PriceHistory\Event\OldChannelPricingLogEntriesEvents;
use Sylius\Component\Core\Repository\ChannelPricingLogEntryRepositoryInterface;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class ChannelPricingLogEntriesRemover implements ChannelPricingLogEntriesRemoverInterface
{
    public function __construct(
        private ChannelPricingLogEntryRepositoryInterface $channelPricingLogEntriesRepository,
        private ObjectManager $channelPricingLogEntriesManager,
        private ClockInterface $clock,
        private EventDispatcherInterface $eventDispatcher,
        private int $batchSize = 100,
    ) {
    }

    public function remove(int $fromDays): void
    {
        $fromDate = $this->getFromDate($fromDays);
        while ([] !== $oldChannelPricingLogEntries = $this->getBatch($fromDate)) {
            foreach ($oldChannelPricingLogEntries as $oldChannelPricingLogEntry) {
                $this->channelPricingLogEntriesManager->remove($oldChannelPricingLogEntry);
            }

            $this->processDeletion($oldChannelPricingLogEntries);
        }
    }

    private function getBatch(\DateTimeInterface $fromDate): array
    {
        return $this->channelPricingLogEntriesRepository->findOlderThan($fromDate, $this->batchSize);
    }

    private function processDeletion(array $deletedChannelPricingLogEntries): void
    {
        $this->eventDispatcher->dispatch(new GenericEvent($deletedChannelPricingLogEntries), OldChannelPricingLogEntriesEvents::PRE_REMOVE);
        $this->channelPricingLogEntriesManager->flush();
        $this->eventDispatcher->dispatch(new GenericEvent($deletedChannelPricingLogEntries), OldChannelPricingLogEntriesEvents::POST_REMOVE);
        $this->channelPricingLogEntriesManager->clear();
    }

    private function getFromDate(int $fromDays): \DateTimeInterface
    {
        $now = $this->clock->now();

        return $now->modify(sprintf('-%d days', $fromDays));
    }
}
