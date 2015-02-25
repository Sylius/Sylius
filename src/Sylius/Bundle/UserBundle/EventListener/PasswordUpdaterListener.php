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
use Symfony\Component\EventDispatcher\GenericEvent;
use Sylius\Component\User\Security\PasswordUpdaterInterface;

/**
 * User update listener.
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class PasswordUpdaterListener
{
    /**
     * @var PasswordUpdaterInterface
     */
    protected $passwordUpdater;

    function __construct(PasswordUpdaterInterface $passwordUpdater)
    {
        $this->passwordUpdater = $passwordUpdater;
    }

    public function updatePassword(UserInterface $user)
    {
        if (null !== $user->getPlainPassword()) {
            $this->passwordUpdater->updatePassword($user);
        }
    }

    public function genericEventUpdater(GenericEvent $event)
    {
        $user = $event->getSubject();

        if (!$user instanceof UserInterface) {
            throw new UnexpectedTypeException(
                $user,
                'Sylius\Component\User\Model\UserInterface'
            );
        }

        $this->updatePassword($user);
    }

    public function prePersist(LifecycleEventArgs $event)
    {
        $item = $event->getEntity();

        if (!$item instanceof UserInterface) {
            return;
        }

        $this->updatePassword($item);
    }

    public function preUpdate(LifecycleEventArgs $event)
    {
        $item = $event->getEntity();

        if (!$item instanceof UserInterface) {
            return;
        }

        $this->updatePassword($item);
    }
}
