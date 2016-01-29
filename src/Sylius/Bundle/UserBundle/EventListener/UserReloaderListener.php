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
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Łukasz CHruściel <lukasz.chrusciel@lakion.com>
 */
class UserReloaderListener
{
    /**
     * @var UserReloaderInterface
     */
    protected $userReloader;

    /**
     * @param UserReloaderInterface $userReloader
     */
    public function __construct(UserReloaderInterface $userReloader)
    {
        $this->userReloader = $userReloader;
    }

    /**
     * @param GenericEvent $event
     */
    public function reloadUser(GenericEvent $event)
    {
        $user = $event->getSubject();

        if (!$user instanceof UserInterface) {
            throw new UnexpectedTypeException(
                $user,
                UserInterface::class
            );
        }

        $this->userReloader->reloadUser($user);
    }
}
