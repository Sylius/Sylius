<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Sylius\Component\User\Model\CustomerInterface;

/**
 * Keeps user's username synchronized with email.
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class DefaultUsernameORMListener
{
    /**
     * @param OnFlushEventArgs $onFlushEventArgs
     */
    public function onFlush(OnFlushEventArgs $onFlushEventArgs)
    {
        $entityManager = $onFlushEventArgs->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();

        $entities = array_merge(
            $unitOfWork->getScheduledEntityInsertions(),
            $unitOfWork->getScheduledEntityUpdates()
        );

        foreach ($entities as $entity) {
            if (!$entity instanceof CustomerInterface) {
                continue;
            }

            $user = $entity->getUser();
            if (null !== $user && $entity->getEmail() !== $user->getUsername()) {
                $user->setUsername($entity->getEmail());
                $entityManager->persist($user);
                $userMetadata = $entityManager->getClassMetadata(get_class($user));
                $unitOfWork->recomputeSingleEntityChangeSet($userMetadata, $user);
            }
        }
    }
}
