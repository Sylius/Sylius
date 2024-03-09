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

namespace Sylius\Bundle\CoreBundle\PriceHistory\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\UnitOfWork;
use Sylius\Bundle\CoreBundle\PriceHistory\EntityObserver\EntityObserverInterface;
use Webmozart\Assert\Assert;

final class OnFlushEntityObserverListener
{
    public function __construct(private iterable $entityObservers)
    {
        Assert::allImplementsInterface($this->entityObservers, EntityObserverInterface::class);
    }

    public function onFlush(OnFlushEventArgs $eventArgs): void
    {
        $entityManager = $eventArgs->getObjectManager();
        $unitOfWork = $entityManager->getUnitOfWork();

        $scheduledEntities = array_merge(
            $unitOfWork->getScheduledEntityInsertions(),
            $unitOfWork->getScheduledEntityUpdates(),
        );

        $atLeastOneEntityChanged = false;

        foreach ($scheduledEntities as $entity) {
            /** @var EntityObserverInterface $entityObserver */
            foreach ($this->entityObservers as $entityObserver) {
                if (
                    !$entityObserver->supports($entity) ||
                    !$this->isEntityChanged($unitOfWork, $entity, $entityObserver->observedFields())
                ) {
                    continue;
                }

                $atLeastOneEntityChanged = true;
                $entityObserver->onChange($entity);
            }
        }

        if ($atLeastOneEntityChanged) {
            $unitOfWork->computeChangeSets();
        }
    }

    private function isEntityChanged(UnitOfWork $unitOfWork, object $entity, array $supportedFields): bool
    {
        $changedFields = array_keys($unitOfWork->getEntityChangeSet($entity));

        return [] !== array_intersect($changedFields, $supportedFields);
    }
}
