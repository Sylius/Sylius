<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\UnitOfWork;
use Sylius\Component\Core\Model\CustomerInterface;

/**
 * Keeps user's username synchronized with email.
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
final class DefaultUsernameORMListener
{
    /**
     * @param OnFlushEventArgs $onFlushEventArgs
     */
    public function onFlush(OnFlushEventArgs $onFlushEventArgs)
    {
        $entityManager = $onFlushEventArgs->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();

        $this->processEntities($unitOfWork->getScheduledEntityInsertions(), $entityManager, $unitOfWork);
        $this->processEntities($unitOfWork->getScheduledEntityUpdates(), $entityManager, $unitOfWork);
    }

    /**
     * @param array $entities
     * @param EntityManagerInterface $entityManager
     */
    private function processEntities($entities, EntityManagerInterface $entityManager, UnitOfWork $unitOfWork)
    {
        foreach ($entities as $customer) {
            if (!$customer instanceof CustomerInterface) {
                continue;
            }

            $user = $customer->getUser();
            if (null === $user) {
                continue;
            }

            if ($customer->getEmail() === $user->getUsername() && $customer->getEmailCanonical() === $user->getUsernameCanonical()) {
                continue;
            }

            $user->setUsername($customer->getEmail());
            $user->setUsernameCanonical($customer->getEmailCanonical());

            /** @var ClassMetadata $userMetadata */
            $userMetadata = $entityManager->getClassMetadata(get_class($user));
            $unitOfWork->recomputeSingleEntityChangeSet($userMetadata, $user);
        }
    }
}
