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

use Doctrine\ORM\Event\LifecycleEventArgs;
use Sylius\Component\User\Model\CustomerInterface;
use Sylius\Component\User\Model\UserInterface;

/**
 * Keeps user's username synchronized with email.
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class DefaultUsernameORMListener
{
    /**
     * @param LifecycleEventArgs $event
     */
    public function prePersist(LifecycleEventArgs $event)
    {
        $user = $event->getEntity();

        if (!$user instanceof UserInterface) {
            return;
        }

        $customer = $user->getCustomer();
        if (null !== $customer && $customer->getEmail() !== $user->getUsername()) {
            $user->setUsername($customer->getEmail());
        }
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function preUpdate(LifecycleEventArgs $event)
    {
        $customer = $event->getEntity();

        if (!$customer instanceof CustomerInterface) {
            return;
        }

        $user = $customer->getUser();
        if (null !== $user && $user->getUsername() !== $customer->getEmail()) {
            $user->setUsername($customer->getEmail());
            $entityManager = $event->getEntityManager();
            $entityManager->persist($user);
            $entityManager->flush($user);
        }
    }
}
