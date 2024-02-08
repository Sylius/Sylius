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

namespace Sylius\Bundle\UserBundle\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Security\PasswordUpdaterInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class PasswordUpdaterListener
{
    public function __construct(private PasswordUpdaterInterface $passwordUpdater)
    {
    }

    public function genericEventUpdater(GenericEvent $event): void
    {
        $this->updatePassword($event->getSubject());
    }

    public function prePersist(LifecycleEventArgs $event): void
    {
        $user = $event->getObject();

        if (!$user instanceof UserInterface) {
            return;
        }

        $this->updatePassword($user);
    }

    public function preUpdate(LifecycleEventArgs $event): void
    {
        $user = $event->getObject();

        if (!$user instanceof UserInterface) {
            return;
        }

        $this->updatePassword($user);
    }

    protected function updatePassword(UserInterface $user): void
    {
        if (null !== $user->getPlainPassword()) {
            $this->passwordUpdater->updatePassword($user);
        }
    }
}
