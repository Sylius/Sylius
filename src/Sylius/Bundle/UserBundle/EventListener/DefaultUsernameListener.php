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
class DefaultUsernameListener
{
    public function prePersist(LifecycleEventArgs $event)
    {
        $item = $event->getEntity();

        if (!$item instanceof UserInterface) {
            return;
        }
        $customer = $item->getCustomer();
        if (null !== $customer && $customer->getEmail() !== $item->getUsername()) {
            $item->setUsername($customer->getEmail());
        }
    }

    public function preUpdate(LifecycleEventArgs $event)
    {
        $item = $event->getEntity();

        if (!$item instanceof CustomerInterface) {
            return;
        }
        $user = $item->getUser();
        if (null !== $user && $user->getUsername() !== $item->getEmail()) {
            $user->setUsername($item->getEmail());
            $entityManager = $event->getEntityManager();
            $entityManager->persist($user);
            $entityManager->flush($user);
        }
    }
}
