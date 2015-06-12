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

use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Event\ResourceEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * User delete listener.
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class UserDeleteListener
{
    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @param SecurityContextInterface $securityContext
     * @param SessionInterface         $session
     */
    public function __construct(SecurityContextInterface $securityContext, SessionInterface $session)
    {
        $this->securityContext = $securityContext;
        $this->session = $session;
    }

    /**
     * @param ResourceEvent $event
     */
    public function deleteUser(ResourceEvent $event)
    {
        $user = $event->getSubject();

        if (!$user instanceof UserInterface) {
            throw new UnexpectedTypeException(
                $user,
                'Sylius\Component\User\Model\UserInterface'
            );
        }

        if (($token = $this->securityContext->getToken()) && ($loggedUser = $token->getUser()) && ($loggedUser->getId() === $user->getId())) {
            $event->stopPropagation();
            $this->session->getBag('flashes')->add('error', 'Cannot remove currently logged in user.');
        }
    }
}
