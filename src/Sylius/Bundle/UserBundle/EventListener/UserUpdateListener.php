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

use Sylius\Bundle\UserBundle\Reloader\UserReloaderInterface;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * User update listener.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class UserUpdateListener
{
    protected $userReloader;

    public function __construct(UserReloaderInterface $userReloader)
    {
        $this->userReloader = $userReloader;
    }

    public function processUser(GenericEvent $event)
    {
        $user = $event->getSubject();

        if (!$user instanceof UserInterface) {
            throw new UnexpectedTypeException(
                $user,
                'Sylius\Component\User\Model\UserInterface'
            );
        }

        $this->userReloader->reloadUser($user);
    }
}
