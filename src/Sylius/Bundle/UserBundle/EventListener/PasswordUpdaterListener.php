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
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Security\PasswordUpdaterInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class PasswordUpdaterListener
{
    /**
     * @var PasswordUpdaterInterface
     */
    protected $passwordUpdater;

    /**
     * @param PasswordUpdaterInterface $passwordUpdater
     */
    public function __construct(PasswordUpdaterInterface $passwordUpdater)
    {
        $this->passwordUpdater = $passwordUpdater;
    }

    /**
     * @param UserInterface $user
     */
    public function updateUserPassword(UserInterface $user)
    {
        if (null !== $user->getPlainPassword()) {
            $this->passwordUpdater->updatePassword($user);
        }
    }

    /**
     * @param GenericEvent $event
     */
    public function genericEventUpdater(GenericEvent $event)
    {
        $user = $event->getSubject();

        if (!$user instanceof UserInterface) {
            throw new UnexpectedTypeException(
                $user,
                UserInterface::class
            );
        }

        $this->updateUserPassword($user);
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function prePersist(LifecycleEventArgs $event)
    {
        $this->updatePassword($event);
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function preUpdate(LifecycleEventArgs $event)
    {
        $this->updatePassword($event);
    }

    /**
     * @param LifecycleEventArgs $event
     */
    protected function updatePassword(LifecycleEventArgs $event)
    {
        $item = $event->getEntity();

        if (!$item instanceof UserInterface) {
            return;
        }

        $this->updateUserPassword($item);
    }
}
