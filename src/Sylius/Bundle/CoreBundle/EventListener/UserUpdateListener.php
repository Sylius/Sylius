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

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Sylius\Component\Resource\Event\ResourceEvent;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;

/**
 * User update listener.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class UserUpdateListener
{
    protected $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    public function processUser(ResourceEvent $event)
    {
        $user = $event->getResource();

        if (!$user instanceof UserInterface) {
            throw new UnexpectedTypeException(
                $user,
                'FOS\UserBundle\Model\UserInterface'
            );
        }

        $this->userManager->reloadUser($user);
    }
}
